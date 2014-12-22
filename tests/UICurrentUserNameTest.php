<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class UICurrentUserNameTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Proxy;
    use UserInputTrait;


    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $config;
    protected $current_user;
    protected $current_user_rights;
    protected $current_user_name;

    public function setUp() {
        $this->config = new Config();
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = RemoteWebDriver::create($this->config->url.':4444/wd/hub', $capabilities);

        $this->addApp();
        $this->createUser();
    }

    protected function addApp()
    {
        $this->app = new App_CLI();
        $this->app->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib','../lib'),
            'mail'=>array('templates/mail'),
        ))->setBasePath('.');
        $this->app->dbConnect();
        $this->app->page = '';
        $this->app->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User', 'email', 'password')
        ;

        return $this->app;
    }

    protected function createUser() {
        $time = time();
        $this->current_user_name = 'UITestuser_'.$time;
        $this->current_user = $this->app->add('Model_User');
        $this->current_user->set('name',$this->current_user_name)
            ->set('email','test_'.$time.'@test.com')
            ->set('password','123123')
            ->save();
        $this->app->addMethod('currentUser',function($user){return $this->current_user;});

        $this->current_user_rights = $this->app->add('Model_User_Right')
            ->setRights($this->current_user->id,['can_see_tasks,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task,can_see_projects,can_see_quotes,can_add_projects,can_edit_projects,can_delete_projects,can_add_quote,can_edit_quote,can_delete_quote,can_submit_for_quotation,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_rates,can_manage_rates,can_see_clients,can_manage_clients,can_see_reports,can_see_deleted,can_restore_deleted,can_see_dashboard,can_move_to_from_archive,can_track_time,can_add_requirement,can_edit_requirement,can_delete_requirement,can_add_comment_to_requirement,can_see_settings,can_edit_settings,can_see_users,can_manage_users,can_see_rates,can_manage_rates,can_see_developers,can_manage_developers,can_see_logs,can_see_dashboard,can_login_as_any_user'])
        ;
    }

    public function tearDown() {
        if( $this->hasFailed() ) {
            $path = $this->config->screenshot_location . "/screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webDriver->takeScreenshot( $path );
        }
        $this->webDriver->close();
        $this->current_user->forceDelete();
        $this->current_user_rights->delete();
    }


    public function testPages() {

        $this->sendConsoleMessage('Wait for page loading...');
        $this->webDriver->get($this->config->test_url);
        $this->waitForUserInput('Done. ');

        // form wrapper
        $user_login_form_wrapper = $this->webDriver->findElement(WebDriverBy::cssSelector('div.user-login-form-wrapper'));
        $form_title = $this->webDriver->findElement(WebDriverBy::xpath(".//h2[text()='Client Log-in']"));
        $this->assertTrue($form_title->getText()=='Client Log-in','Login form title is not correct.');

        // form
        $this->sendConsoleMessage('Filling the form...');
        $login_form  = $user_login_form_wrapper->findElement(WebDriverBy::cssSelector(".user-login-form"));

        $email_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="email"]'));
        $email_field->click();
        $this->webDriver->getKeyboard()->sendKeys($this->current_user->get('email'));

        $password_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="password"]'));
        $password_field->click();
        $this->webDriver->getKeyboard()->sendKeys('123123');
        $this->waitForUserInput('Done. ');


        $submit_button = $login_form->findElement(WebDriverBy::cssSelector('button.atk-button'));
        $submit_button->click();
        $this->waitForUserInput('Wait for form submit and page reload. ');


        // PAGES

        // dashboard
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // tasks
        $this->webDriver->get($this->config->test_url.'?page=tasks');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // quotes
        $this->webDriver->get($this->config->test_url.'?page=quotes');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // projects
        $this->webDriver->get($this->config->test_url.'?page=projects');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // clients
        $this->webDriver->get($this->config->test_url.'?page=clients');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // reports
        $this->webDriver->get($this->config->test_url.'?page=reports');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // developers
        $this->webDriver->get($this->config->test_url.'?page=developers');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // deleted
        $this->webDriver->get($this->config->test_url.'?page=deleted');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // users
        $this->webDriver->get($this->config->test_url.'?page=users');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // logs
        $this->webDriver->get($this->config->test_url.'?page=logs');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // rates
        $this->webDriver->get($this->config->test_url.'?page=rates');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        // account
        $this->webDriver->get($this->config->test_url.'?page=account');
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');

        $this->waitForUserInput('All pages tested! ');
    }





}