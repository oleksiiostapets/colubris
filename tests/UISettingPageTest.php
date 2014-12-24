<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class UISettingPageTest extends PHPUnit_Framework_TestCase {

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
            ->setRights($this->current_user->id,['can_see_settings']) // can_see_settings,can_edit_settings
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



    public function testAccountForm() {

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


        // dashboard
        $cur_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("div.current-user-name"));
        $this->assertTrue($cur_user_name->getText()==$this->current_user_name,'Displayed current user name is not correct');


//        // account
        $this->goToAccountPage();
//        $this->checkNameInputField();
//        $this->updateNameField();
//        $this->checkNameInputField();
//        $this->updateNameField(false);
//        $this->checkNameInputField();
//
//        // mail settings
//        $this->checkMailSettings();
//        $this->checkMailSettings();

        // avatar settings
        $this->checkAvatarSettings();

        $this->waitForUserInput('All pages tested! ');
    }

    protected function goToAccountPage() {
        $this->webDriver->get($this->config->test_url.'?page=account');
        $this->waitForUserInput('Wait until Angular loads all templates.');
    }

    protected function checkNameInputField() {
        $form_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("input.account-name-field"));
        $this->assertTrue($this->current_user_name==$form_user_name->getAttribute('value'),'Form user name is not correct');
        $this->waitForUserInput('Submitted form checked');
    }

    protected function updateNameField($enter=true) {
        $this->current_user_name = $new_name = $this->current_user_name . '_Up';
        $form_user_name = $this->webDriver->findElement(WebDriverBy::cssSelector("input.account-name-field"));
        $this->sendConsoleMessage('Double click on name field to select all.');
        $this->webDriver->getMouse()->doubleClick($form_user_name->getCoordinates());
        $this->webDriver->getKeyboard()->sendKeys($new_name);

        if ($enter) {
            // save form by pressing <enter>
            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
            $this->waitForUserInput('Submitted by pressing <enter>. Wait for form submit...');
        } else {
            // save form by clicking submit button
            $submit_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.account-save-button"));
            $submit_button->click();
            $this->waitForUserInput('Submitted by clicking save button. Wait for form submit...');
        }
    }

    public function checkMailSettings() {
        $send_when_task_changed_checkbox = $this->webDriver->findElement(WebDriverBy::cssSelector("input.send-when-task-changed"));
        $send_when_task_changed_checkbox->click();
        $is_checked = $send_when_task_changed_checkbox->getAttribute('checked');
        $submit = $this->webDriver->findElement(WebDriverBy::cssSelector("button.account-main-settings-submit"));
        $submit->click();
        $this->waitForUserInput('Mail settings updated');

        // reload page and check if data really updated
        $this->goToAccountPage();
        $send_when_task_changed_checkbox_updated = $this->webDriver->findElement(WebDriverBy::cssSelector("input.send-when-task-changed"));
        $is_checked_after = $send_when_task_changed_checkbox_updated->getAttribute('checked');

        $this->assertTrue($is_checked==$is_checked_after,'Send when task changed was not updated after form submit');

    }

    public function checkAvatarSettings() {
        $avatar_field = $this->webDriver->findElement(WebDriverBy::cssSelector("input.avatar-upload"));
        $avatar_field->sendKeys(__DIR__ . '/images/12345.jpg');

        $this->waitForUserInput('=============');

        $submit = $this->webDriver->findElement(WebDriverBy::cssSelector("button.avatar-upload-submit"));
        $submit->click();
        $this->waitForUserInput('Submitted by clicking save button. Wait for form submit...');

    }
}