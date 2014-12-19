<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class LiveClientAccessRightsTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Proxy;


    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $config;
    protected $current_user_email = 'konstantin@agile55.com';
    protected $super_password = '';

    public function setUp() {
        $this->config = new Config();
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = RemoteWebDriver::create($this->config->url.':4444/wd/hub', $capabilities);

    }

    public function tearDown() {
        if( $this->hasFailed() ) {
            $date = "screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webDriver->takeScreenshot( $date );
        }
        $this->webDriver->close();
    }


    public function testPages() {
        $this->webDriver->get($this->config->live_url);
        $this->waitForUserInput();

        // form wrapper
        $user_login_form_wrapper = $this->webDriver->findElement(WebDriverBy::cssSelector('div.user-login-form-wrapper'));
        $form_title = $this->webDriver->findElement(WebDriverBy::xpath(".//h2[text()='Client Log-in']"));
        $this->assertTrue($form_title->getText()=='Client Log-in','Login form title is not correct.');

        // form
        $login_form  = $user_login_form_wrapper->findElement(WebDriverBy::cssSelector(".user-login-form"));

        $email_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="email"]'));
        $email_field->click();
        $this->webDriver->getKeyboard()->sendKeys($this->current_user_email);

        $password_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="password"]'));
        $password_field->click();
        $this->webDriver->getKeyboard()->sendKeys($this->super_password);
        $this->waitForUserInput();


        $submit_button = $login_form->findElement(WebDriverBy::cssSelector('button.atk-button'));
        $submit_button->click();
        $this->waitForUserInput();

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

        // deleted
        $this->webDriver->get($this->config->test_url.'?page=deleted');
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see "deleted" page');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());

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

        $this->waitForUserInput();
    }




    protected function waitForUserInput() {
        if ($this->config->visual_mode) {
            if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
        }
    }

}