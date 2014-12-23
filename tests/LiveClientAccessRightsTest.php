<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class LiveClientAccessRightsTest extends PHPUnit_Framework_TestCase {

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
            $date = "screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webDriver->takeScreenshot( $date );
        }
        $this->webDriver->close();
    }


    private function logIn(){
        $this->sendConsoleMessage('Wait for page loading...');
        $this->webDriver->get($this->config->test_url);
//        $this->waitForUserInput('Done. ');

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
    private function checkTitles(){
        $project_dropdown_title = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .project_filter_title"));
        $this->assertTrue($project_dropdown_title->getText()=='Project:','User cannot see project filter');
        $quote_dropdown_title = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .quote_filter_title"));
        $this->assertTrue($quote_dropdown_title->getText()=='Quote:','User cannot see quote filter');
        $requirement_dropdown_title = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .requirement_filter_title"));
        $this->assertTrue($requirement_dropdown_title->getText()=='Requirement:','User cannot see requirement filter');
        $requirement_dropdown_title = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .status_filter_title"));
        $this->assertTrue($requirement_dropdown_title->getText()=='Status:','User cannot see status filter');
        $requirement_dropdown_title = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .assigned_filter_title"));
        $this->assertTrue($requirement_dropdown_title->getText()=='Assigned:','User cannot see assigned filter');
    }
    private function checkProjectSelector(){
        $project_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_project select option[value='?']"));
        $this->assertTrue($project_dropdown_selector->getText()=='','Invalid default value for project selector');
        $project_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_project select option[value='0']"));
        $this->assertTrue($project_dropdown_selector->getText()=='all','Invalid ALL value for project selector');
        $project_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_project select option[selected='selected']"));
        $this->assertTrue($project_dropdown_selector->getAttribute('value')=='?','Selected project option is invalid');

        //choose the first project
        $project_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_project select"));
        $project_dropdown_selector->click();
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $project_dropdown_selector->click();
    }
    private function checkQuoteSelector(){
        $quote_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_quote select option[value='?']"));
        $this->assertTrue($quote_selector->getText()=='','Invalid default value for quote selector');
        $quote_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_quote select option[value='0']"));
        $this->assertTrue($quote_selector->getText()=='all','Invalid ALL value for quote selector');
        $quote_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_quote select option[selected='selected']"));
        $this->assertTrue($quote_selector->getAttribute('value')=='?','Selected quote option is invalid');

        //choose the first quote
        $quote_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_quote select"));
        $quote_selector->click();
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $quote_selector->click();
    }
    private function checkRequirementSelector(){
        $requirement_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_requirement select option[value='?']"));
        $this->assertTrue($requirement_dropdown_selector->getText()=='','Invalid default value for requirement selector');
        $requirement_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_requirement select option[value='0']"));
        $this->assertTrue($requirement_dropdown_selector->getText()=='all','Invalid ALL value for requirement selector');
        $requirement_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_requirement select option[selected='selected']"));
        $this->assertTrue($requirement_dropdown_selector->getAttribute('value')=='?','Selected requirement option is invalid');

        //choose the first requirement
        $requirement_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_requirement select"));
        $requirement_dropdown_selector->click();
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ARROW_DOWN);
        $requirement_dropdown_selector->click();
    }
    private function checkStatusSelector(){
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='?']"));
        $this->assertTrue($status_dropdown_selector->getText()=='','Invalid default value for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[selected='selected']"));
        $this->assertTrue($status_dropdown_selector->getAttribute('value')=='?','Selected status option is invalid');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='0']"));
        $this->assertTrue($status_dropdown_selector->getText()=='all','Invalid ALL value for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='1']"));
        $this->assertTrue($status_dropdown_selector->getText()=='unstarted','Invalid ALL unstarted for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='2']"));
        $this->assertTrue($status_dropdown_selector->getText()=='started','Invalid ALL started for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='3']"));
        $this->assertTrue($status_dropdown_selector->getText()=='finished','Invalid finished value for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='4']"));
        $this->assertTrue($status_dropdown_selector->getText()=='tested','Invalid tested value for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='5']"));
        $this->assertTrue($status_dropdown_selector->getText()=='rejected','Invalid rejected value for status selector');
        $status_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_status select option[value='6']"));
        $this->assertTrue($status_dropdown_selector->getText()=='accepted','Invalid accepted value for status selector');
    }
    private function checkAssignedSelector(){
        $assigned_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_assigned select option[value='?']"));
        $this->assertTrue($assigned_dropdown_selector->getText()=='','Invalid default value for assigned selector');
        $assigned_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_assigned select option[value='0']"));
        $this->assertTrue($assigned_dropdown_selector->getText()=='all','Invalid ALL value for assigned selector');
        $assigned_dropdown_selector = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_filter .filter_assigned select option[selected='selected']"));
        $this->assertTrue($assigned_dropdown_selector->getAttribute('value')=='?','Selected assigned option is invalid');
    }
    private function checkTableHeaders(){
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_number_header"));
        $this->assertTrue($task_header->getText()=='#','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_name_header"));
        $this->assertTrue($task_header->getText()=='Name','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_project_header"));
        $this->assertTrue($task_header->getText()=='Project','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_quote_header"));
        $this->assertTrue($task_header->getText()=='Quote','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_requirement_header"));
        $this->assertTrue($task_header->getText()=='Requirement','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_priority_header"));
        $this->assertTrue($task_header->getText()=='Priority','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_type_header"));
        $this->assertTrue($task_header->getText()=='Type','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_status_header"));
        $this->assertTrue($task_header->getText()=='Status','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_spent_time_header"));
        $this->assertTrue($task_header->getText()=='Spent Time','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_requester_header"));
        $this->assertTrue($task_header->getText()=='Requester','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_assigned_header"));
        $this->assertTrue($task_header->getText()=='Assigned','Invalid header name');
        $task_header = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_actions_header"));
        $this->assertTrue($task_header->getText()=='Actions','Invalid header name');
    }
    private function checkFirstTask(){
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_number_1"));
        $this->assertTrue($first_task->getText()=='1','Invalid task number');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_name_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty task name');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_project_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty project name');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_quote_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty quote name');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_requirement_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty requirement name');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_priority_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty priority');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_type_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty type');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_status_1"))->getText();
        $this->assertNotEmpty($first_task,'Empty status');


        //if user can edit task he can see edit button
        $u = $this->getUser();
        $r = $u->add('Model_User_Right');

        if($r->canEditTask($u['id'])){
            $this->userCanSeeEditButton();
        }else{
            $this->userCanNotSeeEditButton();
        }

        //if user can delete task he can see delete button
    }

    /**
     * @expectedException NoSuchElementException
     */
    private function userCanNotSeeEditButton(){
        $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_edit_1"))->getText();
    }
    private function userCanSeeEditButton(){
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_edit_1"));
        $this->assertTrue($first_task->getText()=='Edit','Invalid Edit button name');
    }
    public function testPages() {
        $this->logIn();
        $this->webDriver->get($this->config->test_url.'?page=tasks');
        $this->assertContains('Tasks', $this->webDriver->getTitle());

        sleep(1);
        $this->sendConsoleMessage('checkTitles...');
        $this->checkTitles();
        sleep(1);
        $this->sendConsoleMessage('checkProjectSelector...');
        $this->checkProjectSelector();
        sleep(1);
        $this->sendConsoleMessage('checkQuoteSelector...');
        $this->checkQuoteSelector();
        sleep(1);
        $this->sendConsoleMessage('checkRequirementSelector...');
        $this->checkRequirementSelector();
        sleep(1);
        $this->sendConsoleMessage('checkStatusSelector...');
        $this->checkStatusSelector();
        sleep(1);
        $this->sendConsoleMessage('checkAssignedSelector...');
        $this->checkAssignedSelector();
        sleep(1);
        $this->sendConsoleMessage('checkTableHeaders...');
        $this->checkTableHeaders();
        sleep(1);
        $this->sendConsoleMessage('checkFirstTask...');
        $this->checkFirstTask();

        $this->waitForUserInput('Finished');





    }
}