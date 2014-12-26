<?php
/**
 * Created by PhpStorm.
 * User: konstantin
 * Date: 25.12.14
 * Time: 10:12
 */
class LiveClientProjectPageTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Proxy;
    use UserInputTrait;
    use GetUserTrait;

    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $config;

    public function setUp() {
        $this->config = new Config();
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = RemoteWebDriver::create($this->config->url.':4444/wd/hub', $capabilities);

    }

    public function tearDown() {
        if( $this->hasFailed() ) {
            $path = $this->config->screenshot_location . "/screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webDriver->takeScreenshot( $path );
        }
        $this->webDriver->close();
    }

    public function testPages() {
        $this->logIn();
        $this->webDriver->get($this->config->test_url.'?page=projects');
        $this->assertContains('Projects', $this->webDriver->getTitle());


        $this->checkTableHeaders();

        $this->waitForUserInput('Finished');
    }

    private function checkTableHeaders(){
        $this->sendConsoleMessage('checkTableHeaders...');
        $this->waitForUserInput('Hit the Enter');
        $table_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_number_header"));
        $this->assertTrue($table_header->getText()=='#','Invalid header name');
        $table_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_name_header"));
        $this->assertTrue($table_header->getText()=='Name','Invalid header name');
        $table_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_descr_header"));
        $this->assertTrue($table_header->getText()=='Descr','Invalid header name');
        $table_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_actions_header"));
        $this->assertTrue($table_header->getText()=='Actions','Invalid header name');
    }

    private function logIn(){
        $this->sendConsoleMessage('Wait for page loading...');
        $this->webDriver->get($this->config->test_url);

        // form wrapper
        $user_login_form_wrapper = $this->webDriver->findElement(WebDriverBy::cssSelector('div.user-login-form-wrapper'));
        $form_title = $this->webDriver->findElement(WebDriverBy::xpath(".//h2[text()='Client Log-in']"));
        $this->assertTrue($form_title->getText()=='Client Log-in','Login form title is not correct.');

        // form
        $this->sendConsoleMessage('Filling the form...');
        $login_form  = $user_login_form_wrapper->findElement(WebDriverBy::cssSelector(".user-login-form"));

        $email_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="email"]'));
        $email_field->click();
        $this->webDriver->getKeyboard()->sendKeys($this->config->current_user_email);

        $password_field = $login_form->findElement(WebDriverBy::cssSelector('input[data-shortname="password"]'));
        $password_field->click();
        $this->webDriver->getKeyboard()->sendKeys($this->config->super_password);

        $submit_button = $login_form->findElement(WebDriverBy::cssSelector('button.atk-button'));
        $submit_button->click();
        $this->waitForUserInput('Wait for form submit and page reload. ');
    }
}