<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class UIPageAccessLogsRightTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Proxy;
    use UserInputTrait;


    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $config;
    protected $current_user;
    protected $current_user_rights;

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
        $name = 'UITestuser_'.$time;
        $this->current_user = $this->app->add('Model_User');
        $this->current_user->set('name',$name)
            ->set('email','test_'.$time.'@test.com')
            ->set('password','123123')
            ->save();
        $this->app->addMethod('currentUser',function($user){return $this->current_user;});

        $this->current_user_rights = $this->app->add('Model_User_Right')
            ->setRights($this->current_user->id,['can_see_logs'])
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
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see dashboard');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // tasks
        $this->webDriver->get($this->config->test_url.'?page=tasks');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "tasks" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // quotes
        $this->webDriver->get($this->config->test_url.'?page=quotes');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "quotes" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // projects
        $this->webDriver->get($this->config->test_url.'?page=projects');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "projects" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // clients
        $this->webDriver->get($this->config->test_url.'?page=clients');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "clients" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // reports
        $this->webDriver->get($this->config->test_url.'?page=reports');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "reports" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // developers
        $this->webDriver->get($this->config->test_url.'?page=developers');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "developers" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // deleted
        $this->webDriver->get($this->config->test_url.'?page=deleted');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "deleted" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // users
        $this->webDriver->get($this->config->test_url.'?page=users');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "users" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // logs
        $this->webDriver->get($this->config->test_url.'?page=logs');
        $this->assertContains('Logs', $this->webDriver->getTitle());

        // rates
        $this->webDriver->get($this->config->test_url.'?page=rates');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "rates" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        // account
        $this->webDriver->get($this->config->test_url.'?page=account');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "rates" account');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

        $this->waitForUserInput('All pages tested! ');
    }





}