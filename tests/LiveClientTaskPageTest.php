<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class LiveClientTaskPageTest extends PHPUnit_Framework_TestCase {

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
        if($r->canDeleteTask($u['id'])){
            $this->userCanSeeDeleteButton();
        }else{
            $this->userCanNotSeeDeleteButton();
        }
    }
    private function userCanNotSeeDeleteButton(){
        try {
            $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_delete_1"))->getText();
            $this->fail('User CAN see Delete button but should not');
        }catch (NoSuchElementException $expected) {
            return;
        }
    }
    private function userCanSeeDeleteButton(){
        try {
            $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_delete_1"));
            $this->assertTrue($first_task->getText()=='Delete','Invalid Delete button name');
        }catch (NoSuchElementException $expected) {
            $this->fail('User CAN NOT see Delete button but should');
        }
    }
    private function userCanNotSeeEditButton(){
        try {
            $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_edit_1"))->getText();
            $this->fail('User CAN see Edit button but should not');
        }catch (NoSuchElementException $expected) {
            return;
        }
    }
    private function userCanSeeEditButton(){
        try {
            $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_edit_1"));
            $this->assertTrue($first_task->getText()=='Edit','Invalid Edit button name');
        }catch (NoSuchElementException $expected) {
            $this->fail('User CAN NOT see Edit button but should');
        }
    }
    private function goToEdit(){
        //go to
        $this->waitForUserInput('Go to task form');
        $edit_button = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_actions_1 .task_action_edit_1"));
        $edit_button->click();
    }
    private function checkTaskTitles(){
        $this->waitForUserInput('Compare task data');

        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_name_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_name"))->getAttribute('value');
        $this->assertEquals($first_task,$current_task,'Task name is invalid');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_priority_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_priority"))->getAttribute('value');
        $this->assertEquals($first_task,$current_task,'Task priority is invalid');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_type_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_type"))->getAttribute('value');
        $this->assertEquals($first_task,$current_task,'Task type is invalid');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_status_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_status"))->getAttribute('value');
        $this->assertEquals($first_task,$current_task,'Task status is invalid');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_requester_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_requester option[selected='selected']"))->getText();
        $this->assertEquals($first_task,$current_task,'Task requester is invalid');
        $first_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_crud .task_row_1 .task_assigned_1"))->getText();
        $current_task = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #task_assigned option[selected='selected']"))->getText();
        $this->assertEquals($first_task,$current_task,'Task assigned is invalid');
    }
    private function userCanSeeTime(){
        try {
            $first_time = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_list .task_time_1 .spent_time_row_1 .spent_time_title_1"))->getText();
            $this->assertEquals('Spent time:', $first_time,'Invalid Spent time title');
        }catch (NoSuchElementException $expected) {
            $this->fail('User CANNOT see Time but should');
        }
    }
    private function userCanNotSeeTime(){
        try {
            $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_list .task_time_1 .spent_time_row_1 .spent_time_title_1"))->getText();
            $this->fail('User CAN see Time but should not');
        }catch (NoSuchElementException $expected) {
            return;
        }
    }
    private function userCanSeeTimeForm(){
        try {
            $time_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form .spent_time_title"))->getText();
            $this->assertEquals('Spent Time:', $time_form,'Invalid Spent time title');
            $time_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form .date_title"))->getText();
            $this->assertEquals('Date:', $time_form,'Invalid date title');
            $time_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form .comment_title"))->getText();
            $this->assertEquals('Comment:', $time_form,'Invalid Comment title');
        }catch (NoSuchElementException $expected) {
            $this->fail('User CANNOT see Time Form but should');
        }
    }
    private function userCanNotSeeTimeForm(){
        try {
            $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form"));
            $this->fail('User CAN see Time Form but should not');
        }catch (NoSuchElementException $expected) {
            return;
        }
    }
    private function userCanAddTime(){
        $this->sendConsoleMessage('Filling the form...');

        $time_form_spent_time_field  = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form #spent_time"));
        $time_form_spent_time_field->click();
        $this->webDriver->getKeyboard()->sendKeys(5);

        sleep(1);
        $time_form_time_comment_field  = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form #time_comment"));
        $time_form_time_comment_field->click();
        $this->webDriver->getKeyboard()->sendKeys('test time');
        sleep(1);
        $time_form_add_time_button  = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_time_form .add_time_button"));
        $time_form_add_time_button->click();
        $this->sendConsoleMessage('Wait for form submitting...');

        $this->waitForUserInput('Check if time list has been reloaded');

        $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $last_element = $time_list[count($time_list)-1];

        $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_comment_field"));
        $this->assertEquals('test time', $last_time_box_element->getText(),'Invalid Comment value');
        $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_spent_time_field"));
        $this->assertEquals(5, $last_time_box_element->getText(),'Invalid Spent time value');
        $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_date_field"));
        $this->assertEquals(date('Y-m-d'), $last_time_box_element->getText(),'Invalid date value');
        $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_user_field"));
        $this->assertEquals($this->u['name'], $last_time_box_element->getText(),'Invalid User Name value');
    }
    private function canDeleteOwnTime(){
        $this->waitForUserInput('Check if user can Delete time');
        try{
            $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
            $last_element = $time_list[count($time_list)-1];
            $element_id = $last_element->findElement(WebDriverBy::cssSelector(".time_comment_id"))->getText();

            $delete_button  = $last_element->findElement(WebDriverBy::cssSelector("#close-time-button"));
            $this->assertTrue($delete_button->isDisplayed(),'Delete button is absent');
            $delete_button->click();
            $this->waitForUserInput('Time must be deleted now');

            //check last element is absent
            $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
            $last_element = $time_list[count($time_list)-1];
            $current_element_id = $last_element->findElement(WebDriverBy::cssSelector(".time_comment_id"))->getText();

            $this->assertNotEquals($element_id,$current_element_id,'Element has not been deleted');
        }catch (Exception $e){
            $this->fail('User cannot delete his own time: '.$e->getMessage());
        }
    }
    private function canEditOwnTime(){
        $this->waitForUserInput('Check if user can Edit time');
        try{
            $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
            $last_element = $time_list[count($time_list)-1];

            //edit comment
            $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_comment_field"));
            $last_time_box_element->click();
            sleep(1);
            $last_time_box_element_form  = $last_element->findElement(WebDriverBy::cssSelector("form textarea"));
            $last_time_box_element_form->click();
            sleep(1);
            $last_time_box_element_form->clear();
            $last_time_box_element_form->sendKeys('test time updated');
            $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
            $last_element = $time_list[count($time_list)-1];
            $last_time_box_element_form  = $last_element->findElement(WebDriverBy::cssSelector("form button[type='submit']"));
            $last_time_box_element_form->click();

            //edit spent time
            $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_spent_time_field"));
            $last_time_box_element->click();
            sleep(1);
            $last_time_box_element_form  = $last_element->findElement(WebDriverBy::cssSelector("form input"));
            $last_time_box_element_form->click();
            sleep(1);
            $last_time_box_element_form->clear();
            $last_time_box_element_form->sendKeys(3);
            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

            //edit date
            $last_time_box_element  = $last_element->findElement(WebDriverBy::cssSelector(".time_date_field"));
            $last_time_box_element->click();
            sleep(1);
            $last_time_box_element_form  = $last_element->findElement(WebDriverBy::cssSelector("form input"));
            $last_time_box_element_form->click();
            sleep(1);
            $last_time_box_element_form->clear();
            $last_time_box_element_form->sendKeys('2014-10-14');
            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        }catch (Exception $e){
            $this->fail('User cannot edit time: '.$e->getMessage());
        }

        $this->waitForUserInput('Wait');
    }
    private function canNotDeleteOthersTime(){
        $this->waitForUserInput('Check user cannot delete others time');
        //find last element
        $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $others_time = null;
        foreach($time_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".time_user_field"))->getText() == $this->u['name']){
                continue;
            }
            $others_time = $element;
        }
        if(!$others_time){
            $this->fail('No others time present. Not a bug. Just need to choose other task.');
        }
        try{
            $others_time->findElement(WebDriverBy::cssSelector("#close-time-button"));
            $this->fail('Delete button is here.');
        }catch (NoSuchElementException $e){}
    }
    private function canNotEditOthersTime(){
        $this->waitForUserInput('Check canNotEditOthersTime');
        //find others element
        $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $others_time = null;
        foreach($time_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".time_user_field"))->getText() == $this->u['name']){
                continue;
            }
            $others_time = $element;
        }
        if(!$others_time){
            $this->fail('No others time present. Not a bug. Just need to choose other task.');
        }
        try{
            //edit comment
            $others_time_box_element  = $others_time->findElement(WebDriverBy::cssSelector(".time_comment_field"));
            $others_time_box_element->click();
            sleep(1);
            $others_time_box_element_form  = $others_time->findElement(WebDriverBy::cssSelector("form textarea"));
            $others_time_box_element_form->click();
            sleep(1);
            $others_time_box_element_form->clear();
            $others_time_box_element_form->sendKeys('test time updated');

            $others_time_box_element_form  = $others_time->findElement(WebDriverBy::cssSelector("form button[type='submit']"));
            $others_time_box_element_form->click();
            sleep(1);
            $this->fail('User CAN edit comment in others time');
        }catch (NoSuchElementException $e){}
        try{
            $others_time_box_element  = $others_time->findElement(WebDriverBy::cssSelector(".time_spent_time_field"));
            $others_time_box_element->click();
            sleep(1);
            $others_time_box_element_form  = $others_time->findElement(WebDriverBy::cssSelector("form input"));
            $others_time_box_element_form->click();
            sleep(1);
            $others_time_box_element_form->clear();
            $others_time_box_element_form->sendKeys(111);

            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
            sleep(1);
            $this->fail('User CAN edit spent time in others time');
        }catch (NoSuchElementException $e){}
        try{
            $others_time_box_element  = $others_time->findElement(WebDriverBy::cssSelector(".time_date_field"));
            $others_time_box_element->click();
            sleep(1);
            $others_time_box_element_form  = $others_time->findElement(WebDriverBy::cssSelector("form input"));
            $others_time_box_element_form->click();
            sleep(1);
            $others_time_box_element_form->clear();
            $others_time_box_element_form->sendKeys(2010-10-10);

            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
            sleep(1);
            $this->fail('User CAN edit date in others time');
        }catch (NoSuchElementException $e){}
    }
    private function canNotEditOwnTime(){
        $this->waitForUserInput('Check canNotEditOwnTime');

        //find own element
        $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $own_time = null;
        foreach($time_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".time_user_field"))->getText() != $this->u['name']){
                continue;
            }
            $own_time = $element;
        }
        if(!$own_time){
            $this->fail('No own time present. Not a bug. Just need to choose other task.');
        }
        try{
            //edit comment
            $own_time_box_element  = $own_time->findElement(WebDriverBy::cssSelector(".time_comment_field"));
            $own_time_box_element->click();
            sleep(1);
            $own_time_box_element_form  = $own_time->findElement(WebDriverBy::cssSelector("form textarea"));
            $own_time_box_element_form->click();
            sleep(1);
            $own_time_box_element_form->clear();
            $own_time_box_element_form->sendKeys('test time updated');

            $own_time_box_element_form  = $own_time->findElement(WebDriverBy::cssSelector("form button[type='submit']"));
            $own_time_box_element_form->click();
            sleep(1);
            $this->fail('User CAN edit comment in own time');
        }catch (NoSuchElementException $e){}
        try{
            $others_time_box_element  = $own_time->findElement(WebDriverBy::cssSelector(".time_spent_time_field"));
            $others_time_box_element->click();
            sleep(1);
            $others_time_box_element_form  = $own_time->findElement(WebDriverBy::cssSelector("form input"));
            $others_time_box_element_form->click();
            sleep(1);
            $others_time_box_element_form->clear();
            $others_time_box_element_form->sendKeys(111);

            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
            sleep(1);
            $this->fail('User CAN edit spent time in own time');
        }catch (NoSuchElementException $e){}
        try{
            $others_time_box_element  = $own_time->findElement(WebDriverBy::cssSelector(".time_date_field"));
            $others_time_box_element->click();
            sleep(1);
            $others_time_box_element_form  = $own_time->findElement(WebDriverBy::cssSelector("form input"));
            $others_time_box_element_form->click();
            sleep(1);
            $others_time_box_element_form->clear();
            $others_time_box_element_form->sendKeys(2010-10-10);

            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
            sleep(1);
            $this->fail('User CAN edit date in own time');
        }catch (NoSuchElementException $e){}
    }
    private function canNotDeleteOwnTime(){
        $this->waitForUserInput('Check canNotDeleteOwnTime');

        //find own element
        $time_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $own_time = null;
        foreach($time_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".time_user_field"))->getText() != $this->u['name']){
                continue;
            }
            $own_time = $element;
        }
        if(!$own_time){
            $this->fail('No others time present. Not a bug. Just need to choose other task.');
        }
        try{
            $own_time->findElement(WebDriverBy::cssSelector("#close-time-button"));
            $this->fail('Delete button is here.');
        }catch (NoSuchElementException $e){}
    }
    private function checkTaskTime(){
        $this->waitForUserInput('Check user rights to see/track time');

        $this->u = $this->getUser();
        $r = $this->u->add('Model_User_Right');

        if($r->canSeeTime($this->u['id'])){
            $this->userCanSeeTime();
        }else{
            $this->userCanNotSeeTime();
        }
        if($r->canTrackTime($this->u['id'])){
            $this->userCanSeeTimeForm();
            $this->userCanAddTime();
            $this->canEditOwnTime();
            $this->canNotEditOthersTime();
            $this->canDeleteOwnTime();
            $this->canNotDeleteOthersTime();
        }else{
            $this->userCanNotSeeTimeForm();
            $this->canNotEditOthersTime();
            $this->canNotEditOwnTime();
            $this->canNotDeleteOthersTime();
            $this->canNotDeleteOwnTime();
        }
    }
    private function closeEditFrame(){
        $this->waitForUserInput('Close edit frame');
        $close_button = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form #close-button"));
        $close_button->click();
        $edit_frame = $this->webDriver->findElement(WebDriverBy::cssSelector(".task_edit_form"));
        $this->assertNotTrue($edit_frame->isDisplayed(),'Edit frame has not been closed');
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

        //Edit task tests
        $this->goToEdit();

        $this->checkTaskTitles();
        $this->checkTaskTime();

        $this->closeEditFrame();

        $this->waitForUserInput('Finished');
    }
}