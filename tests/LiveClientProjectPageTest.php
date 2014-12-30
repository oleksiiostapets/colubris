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
    public $project_name;

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

        $this->u = $this->getUser();
        $r = $this->u->add('Model_User_Right');

        if($r->canAddProjects($this->u['id'])){
            $this->canSeeAddButton();
            $this->canCreateProject();
        }else{
            $this->canNotSeeAddButton();
            $this->canNotCreateProject();
        }

        $this->checkFirstProject();

        $this->checkProjectIsTheSame();

        if($r->canEditProjects($this->u['id'])){
            $this->canSeeEditButton();
            $this->canSeeSaveButton();
            $this->canEditProject();
        }else{
            $this->canNotSeeEditButton();
            $this->canNotSeeSaveButton();
        }

        if($r->canAddQuote($this->u['id'])){
            $this->canAddQuote();
        }else{
            $this->canNotSeeAddQuoteForm();
        }

        if($r->canDeleteQuote($this->u['id'])){
            $this->canSeeOwnQuoteDeleteButton();
            $this->canNotSeeOthersQuoteDeleteButton();
            $this->canDeleteOwnQuote();
        }else{
            $this->canNotSeeOthersQuoteDeleteButton();
        }

        if($r->canDeleteProjects($this->u['id'])){
            $this->canSeeDeleteButton();
            $this->canSeeProjectDeleteButton();
            $this->canDeleteProject();
        }else{
            $this->canNotSeeDeleteButton();
            $this->canNotSeeProjectDeleteButton();
        }

        $this->waitForUserInput('Finished');
    }

    private function canDeleteOwnQuote(){
        $this->sendConsoleMessage('canDeleteOwnQuote...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }

        //find own element
        $quote_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".quote_box"));
        $own_quote = null;
        foreach($quote_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".quote_user_name"))->getText() != $this->u['name']){
                continue;
            }
            $own_quote = $element;
        }
        if(!$own_quote){
            $this->fail('No own quote present. Not a bug. Just need to choose other project.');
        }
        $own_quote_name = $own_quote->findElement(WebDriverBy::cssSelector(".quote_name"))->getText();

        $delete_button = $own_quote->findElement(WebDriverBy::cssSelector("#close-time-button"));
        $delete_button->click();
        $this->waitForUserInput('Check if quote was deleted');
        try{
            $this->webDriver->findElement(WebDriverBy::xpath("//span[text()='".$own_quote_name."']"));
            $this->fail('Quote was not deleted');
        }catch (NoSuchElementException $e){
            $this->sendConsoleMessage('Quote was deleted successfully');
        }
    }

    private function canNotSeeOthersQuoteDeleteButton(){
        $this->sendConsoleMessage('canNotSeeOthersQuoteDeleteButton...');

        //find others element
        $quote_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".task_time_box"));
        $others_quote = null;
        foreach($quote_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".time_user_field"))->getText() == $this->u['name']){
                continue;
            }
            $others_quote = $element;
        }
        if(!$others_quote){
            $this->fail('No others quote present. Not a bug. Just need to choose other project.');
        }

        $this->assertFalse(
            $others_quote->findElement(WebDriverBy::cssSelector("#close-time-button"))->isDisplayed(),
            'User can see delete button on others quote'
        );
    }
    private function canSeeOwnQuoteDeleteButton(){
        $this->sendConsoleMessage('canSeeOwnQuoteDeleteButton...');

        //find own element
        $quote_list  = $this->webDriver->findElements(WebDriverBy::cssSelector(".quote_box"));
        $own_quote = null;
        foreach($quote_list as $element){
            if($element->findElement(WebDriverBy::cssSelector(".quote_user_name"))->getText() != $this->u['name']){
                continue;
            }
            $own_quote = $element;
        }
        if(!$own_quote){
            $this->fail('No own quote present. Not a bug. Just need to choose other project.');
        }
        $this->assertTrue(
            $own_quote->findElement(WebDriverBy::cssSelector("#close-time-button"))->isDisplayed(),
            'User can not see delete button on its own quote'
        );

    }

    private function canNotSeeAddQuoteForm(){
        $this->sendConsoleMessage('canNotSeeAddQuoteForm...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }

        $this->assertFalse(
            $this->webDriver->findElement(WebDriverBy::cssSelector(".add_quote_form"))->isDisplayed(),
            'User can see Add Quote form but should not'
        );
    }

    private function canAddQuote(){
        $this->sendConsoleMessage('canAddQuote...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }

        $current_project_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_quotes .add_quote_form"));
        $this->assertTrue($current_project_form->isDisplayed(),'User cannot see add quote form');

        $test_quote_name = 'test_quote_'.time();
        $test_quote_descr = 'test_description_'.time();

        $current_project_form_element = $current_project_form->findElement(WebDriverBy::cssSelector("#quote_name"));
        $current_project_form_element->click();
        $current_project_form_element->sendKeys($test_quote_name);

        $current_project_form_element = $current_project_form->findElement(WebDriverBy::cssSelector("#quote_description"));
        $current_project_form_element->click();
        $current_project_form_element->sendKeys($test_quote_descr);

        $current_project_form->findElement(WebDriverBy::cssSelector(".add_quote_button"))->click();

        $this->waitForUserInput('Check if quote was added');

        $quote_list = $this->webDriver->findElements(WebDriverBy::cssSelector(".project_frame .quote_box"));
        $last_element = $quote_list[count($quote_list)-1];

        $this->assertEquals(
            $test_quote_name,
            $last_element->findElement(WebDriverBy::cssSelector(".quote_name"))->getText(),
            'Quote was not added'
        );
        $this->assertEquals(
            $test_quote_descr,
            $last_element->findElement(WebDriverBy::cssSelector(".quote_descr"))->getText(),
            'Quote was not added'
        );
    }

    private function canDeleteProject(){
        $this->sendConsoleMessage('canDeleteProject...');
        if($this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->closeFrame();
            sleep(1);
        }

        $project_row = $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$this->project_name."']/parent::tr"));
        try{
            $project_row->findElement(WebDriverBy::cssSelector(".project_crud_actions .delete_button"))->click();
        }catch (UnexpectedAlertOpenException $e){
            $this->waitForUserInput('Please confirm the alert message in the browser!!!!!!!!!!!');
            try{
                $project_row = $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$this->project_name."']/parent::tr"));
                $this->fail('Project was not deleted');
            }catch (NoSuchElementException $e){
                $this->sendConsoleMessage('Project was deleted successfully');
            }
        }

    }

    private function closeFrame(){
        $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame #close-button"))->click();
    }

    private function canNotSeeProjectDeleteButton(){
        $this->sendConsoleMessage('canNotSeeProjectDeleteButton...');
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $current_project_buttons->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->fail('User can see Delete button but should not');
        }catch (NoSuchElementException $e){}
    }

    private function canSeeProjectDeleteButton(){
        $this->sendConsoleMessage('canSeeProjectDeleteButton...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit();
        }
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $delete_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue($delete_button->getText() == 'Delete','Delete button label is invalid');
        }catch (NoSuchElementException $e){
            $this->fail('Delete button is absent');
        }
    }

    private function canEditProject(){
        $this->sendConsoleMessage('canEditProject...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }
        sleep(1);
        $current_project_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_form"));

        //save name
        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_name"));
        $current_project_form_field_value = $current_project_form_field->getAttribute('value');
        $this->project_name = $current_project_form_field_new_value = $current_project_form_field_value.'updated';
        $current_project_form_field->click();
        $current_project_form_field->clear();
        $this->webDriver->getKeyboard()->sendKeys($current_project_form_field_new_value);
        sleep(1);
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

        $this->waitForUserInput('canEditProject');
        try{
            $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$current_project_form_field_new_value."']"));
        }catch (NoSuchElementException $e){
            $this->fail('Project was not updated');
        }

        //save descr
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }
        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_descr"));
        $current_project_form_field_value = $current_project_form_field->getAttribute('value');
        $current_project_form_field_new_value = $current_project_form_field_value.'updated';
        $current_project_form_field->click();
        $current_project_form_field->clear();
        $this->webDriver->getKeyboard()->sendKeys($current_project_form_field_new_value);
        sleep(1);
        $current_project_form->findElement(WebDriverBy::cssSelector(".project_buttons .save_button"))->click();
        sleep(1);
        $project_row = $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$current_project_form_field_new_value."']/parent::tr"));
        $project_descr = $project_row->findElement(WebDriverBy::cssSelector(".project_crud_descr"));

        $this->assertTrue($project_descr->getText() == $current_project_form_field_new_value,'Project description did not match with saved one');
        $this->assertNotTrue($project_descr->getText() == $current_project_form_field_value,'Project description was not saved');
    }

    private function canNotSeeSaveButton(){
        $this->sendConsoleMessage('canNotSeeSaveButton...');
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $save_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".save_button"));
            $this->fail('User can see Save button but should not');
        }catch (NoSuchElementException $e){}
    }

    private function canSeeSaveButton(){
        $this->waitForUserInput('canSeeSaveButton');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit($this->project_name);
        }

        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $save_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".save_button"));
            $this->assertTrue($save_button->getText() == 'Save','Save button label is invalid. '.$save_button->getText());
        }catch (NoSuchElementException $e){
            $this->fail('Save button is absent');
        }
    }


    private function checkProjectIsTheSame(){
        $this->sendConsoleMessage('checkProjectIsTheSame...');
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit();
        }
        $this->waitForUserInput('Wait for frame loading...');

        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        $current_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"));

        $this->assertEquals(
            $first_project->findElement(WebDriverBy::cssSelector(".project_crud_name"))->getText(),
            $current_project->findElement(WebDriverBy::cssSelector("#project_name"))->getAttribute('value'),
            'Project is not the same as selected'
        );
        $this->assertEquals(
            $first_project->findElement(WebDriverBy::cssSelector(".project_crud_descr"))->getText(),
            $current_project->findElement(WebDriverBy::cssSelector("#project_descr"))->getAttribute('value'),
            'Project is not the same as selected'
        );
        $this->closeFrame();

    }
    public $count = 0;
    private function goToEdit($project_name=null){
        $this->count++;
//        var_dump($this->count);
        //go to
        $this->waitForUserInput('Go to project form');
//        var_dump('projevt = '.$project_name);
        if($project_name){
            $project_row = $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$project_name."']/parent::tr"));
            $edit_button = $project_row->findElement(WebDriverBy::cssSelector(".edit_button"));
        }else{
            $edit_button = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1 .edit_button"));
        }
        $edit_button->click();
    }

    private  function canNotSeeDeleteButton(){
        $this->sendConsoleMessage('canNotSeeDeleteButton...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $delete_button = $first_project->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue(false,'User can see delete button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private  function canSeeDeleteButton(){
        $this->sendConsoleMessage('canSeeDeleteButton...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $delete_button = $first_project->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue($delete_button->getText()=='Delete','Label of delete button is invalid');
        }catch (NoSuchElementException $e){
            $this->assertTrue(false,'User can not see delete button but should');
        }

    }

    private  function canNotSeeEditButton(){
        $this->sendConsoleMessage('canNotSeeEditButton...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $first_project->findElement(WebDriverBy::cssSelector(".edit_button"));
            $this->assertTrue(false,'User can see edit button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private  function canSeeEditButton(){
        $this->sendConsoleMessage('canSeeEditButton...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $edit_button = $first_project->findElement(WebDriverBy::cssSelector(".edit_button"));
            $this->assertTrue($edit_button->getText()=='Edit','Label of Edit button is invalid');
        }catch (NoSuchElementException $e){
            $this->assertTrue(false,'User can not see edit button but should');
        }

    }

    private function checkFirstProject(){
        $this->sendConsoleMessage('checkFirstProject...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));

        $first_project_element = $first_project->findElement(WebDriverBy::cssSelector(".project_crud_index"));
        $this->assertTrue($first_project_element->getText()=='1','Invalid project number');

        $first_project_element = $first_project->findElement(WebDriverBy::cssSelector(".project_crud_name"));
        $this->assertNotEmpty($first_project_element->getText(),'Empty project name');
    }

    private function canNotSeeAddButton(){
        $this->sendConsoleMessage('canNotSeeAddButton...');
        try{
            $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"));
            $this->assertTrue(false,'User CAN see ADD button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private function canNotCreateProject(){
        $this->sendConsoleMessage('canNotCreateProject...');
        if($this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->closeFrame();
        }
        try{
            $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"));
            $this->assertFalse($this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"))->isDisplayed(),'User can see Add button');
        }catch (Exception $e){}
    }
    private function canCreateProject(){
        $this->sendConsoleMessage('canCreateProject...');
        if($this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->closeFrame();
        }
        $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"))->click();
        sleep(1);
        $current_project_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_form"));

        $this->project_name = 'Test Project '.time();

        $current_project_form->findElement(WebDriverBy::cssSelector("#project_name"))->click();
        $this->webDriver->getKeyboard()->sendKeys($this->project_name);
        $current_project_form->findElement(WebDriverBy::cssSelector("#project_descr"))->click();
        $this->webDriver->getKeyboard()->sendKeys('Test Project description');
        sleep(1);
        $current_project_form->findElement(WebDriverBy::cssSelector(".project_buttons .save_button"))->click();

        $this->waitForUserInput('Continue');
        try{
            $this->webDriver->findElement(WebDriverBy::xpath("//td[text()='".$this->project_name."']"));
        }catch (NoSuchElementException $e){
            $this->fail('No such a project created');
        }
        $this->sendConsoleMessage('Project has been created successfully');
    }
    private function canSeeAddButton(){
        $this->sendConsoleMessage('canSeeAddButton...');
        try{
            $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"));
        }catch (NoSuchElementException $e){
            $this->assertTrue(false,'User CANNOT see ADD button but should');
        }
    }

    private function checkTableHeaders(){
        $this->waitForUserInput('checkTableHeaders...');
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