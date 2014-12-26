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

        $this->u = $this->getUser();
        $r = $this->u->add('Model_User_Right');

        if($r->canAddProjects($this->u['id'])){
            $this->canSeeAddButton();
            $this->canCreateProject();
        }else{
            $this->canNotSeeAddButton();
        }

//        $this->checkFirstProject();

        /*if($r->canEditProjects($this->u['id'])){
            $this->canSeeEditButton();
        }else{
            $this->canNotSeeEditButton();
        }

        if($r->canDeleteProjects($this->u['id'])){
            $this->canSeeDeleteButton();
        }else{
            $this->canNotSeeDeleteButton();
        }*/

//        $this->goToEdit();

//        $this->checkProjectIsTheSame();

        /*if($r->canEditProjects($this->u['id'])){
            $this->canSeeSaveButton();
            $this->canSaveProject();
        }else{
            $this->canNotSeeSaveButton();
            $this->canNotSaveProject();
        }*/

        /*if($r->canDeleteProjects($this->u['id'])){
            $this->canSeeProjectDeleteButton();
            $this->canDeleteProject();
        }else{
            $this->canNotSeeProjectDeleteButton();
        }*/

        $this->waitForUserInput('Finished');
    }

    private function canNotDeleteProject(){}//TODO

    private function canDeleteProject(){
        $this->createTestProject();
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit();
        }
    }//TODO

    private function createTestProject(){

    }
    private function closeFrame(){
        $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame #close-button"))->click();
    }

    private function canNotSeeProjectDeleteButton(){
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $current_project_buttons->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->fail('User can see Delete button but should not');
        }catch (NoSuchElementException $e){}
    }

    private function canSeeProjectDeleteButton(){
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $delete_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue($delete_button->getText() == 'Delete','Delete button label is invalid');
        }catch (NoSuchElementException $e){
            $this->fail('Delete button is absent');
        }
    }

    private function canNotSaveProject(){
        //TODO Don't know how it will work
    }

    private function canSaveProject(){
        $current_project_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_form"));

        //save name
        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_name"));
        $current_project_form_field_value = $current_project_form_field->getAttribute('value');
        $current_project_form_field->click();
        $current_project_form_field->clear();
        $this->webDriver->getKeyboard()->sendKeys($current_project_form_field_value.'updated');
        sleep(1);
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        sleep(1);
        $first_project_name = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1 .project_crud_name"));
        $this->assertTrue($first_project_name->getText() == $current_project_form_field_value.'updated','Project name did not match with saved one');
        $this->assertNotTrue($first_project_name->getText() == $current_project_form_field_value,'Project name was not saved');

        //save descr
        if(!$this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->goToEdit();
        }
        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_descr"));
        $current_project_form_field_value = $current_project_form_field->getAttribute('value');
        $current_project_form_field->click();
        $current_project_form_field->clear();
        $this->webDriver->getKeyboard()->sendKeys($current_project_form_field_value.'updated');
        sleep(1);
        $current_project_form->findElement(WebDriverBy::cssSelector(".project_buttons .save_button"))->click();
        sleep(1);
        $first_project_descr = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1 .project_crud_descr"));
        $this->assertTrue($first_project_descr->getText() == $current_project_form_field_value.'updated','Project description did not match with saved one');
        $this->assertNotTrue($first_project_descr->getText() == $current_project_form_field_value,'Project description was not saved');
    }

    private function canNotSeeSaveButton(){
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $save_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".save_button"));
            $this->fail('User can see Save button but should not');
        }catch (NoSuchElementException $e){}
    }

    private function canSeeSaveButton(){
        $current_project_buttons = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_buttons"));
        try{
            $save_button = $current_project_buttons->findElement(WebDriverBy::cssSelector(".save_button"));
            $this->assertTrue($save_button->getText() == 'Save','Save button label is invalid');
        }catch (NoSuchElementException $e){
            $this->fail('Save button is absent');
        }
    }


    private function checkProjectIsTheSame(){
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

    }
    private function goToEdit(){
        //go to
        $this->waitForUserInput('Go to project form');
        $edit_button = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1 .edit_button"));
        $edit_button->click();
    }

    private  function canNotSeeDeleteButton(){
//        $this->waitForUserInput('canNotSeeDeleteButton...');

        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $delete_button = $first_project->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue(false,'User can see delete button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private  function canSeeDeleteButton(){
//        $this->waitForUserInput('canSeeDeleteButton...');

        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $delete_button = $first_project->findElement(WebDriverBy::cssSelector(".delete_button"));
            $this->assertTrue($delete_button->getText()=='Delete','Label of delete button is invalid');
        }catch (NoSuchElementException $e){
            $this->assertTrue(false,'User can not see delete button but should');
        }

    }

    private  function canNotSeeEditButton(){
//        $this->waitForUserInput('canNotSeeEditButton...');

        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $edit_button = $first_project->findElement(WebDriverBy::cssSelector(".edit_button"));
            $this->assertTrue(false,'User can see edit button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private  function canSeeEditButton(){
//        $this->waitForUserInput('canSeeEditButton...');

        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));
        try{
            $edit_button = $first_project->findElement(WebDriverBy::cssSelector(".edit_button"));
            $this->assertTrue($edit_button->getText()=='Edit','Label of Edit button is invalid');
        }catch (NoSuchElementException $e){
            $this->assertTrue(false,'User can not see edit button but should');
        }

    }

    private function checkFirstProject(){
//        $this->waitForUserInput('checkFirstProject...');
        $first_project = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud .project_crud_row_1"));

        $first_project_element = $first_project->findElement(WebDriverBy::cssSelector(".project_crud_index"));
        $this->assertTrue($first_project_element->getText()=='1','Invalid project number');

        $first_project_element = $first_project->findElement(WebDriverBy::cssSelector(".project_crud_name"));
        $this->assertNotEmpty($first_project_element->getText(),'Empty project name');
    }

    private function canNotSeeAddButton(){
//        $this->waitForUserInput('canNotSeeAddButton...');
        try{
            $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"));
            $this->assertTrue(false,'User CAN see ADD button but should NOT');
        }catch (NoSuchElementException $e){}
    }

    private function canNotCreateProject(){}//TODO
    private function canCreateProject(){
        if($this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame"))->isDisplayed()){
            $this->closeFrame();
        }
        $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud_wrapper .add_project_button"))->click();
        sleep(1);
        $current_project_form = $this->webDriver->findElement(WebDriverBy::cssSelector(".project_frame .project_form"));

        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_name"));
        $current_project_form_field->click();
        $this->webDriver->getKeyboard()->sendKeys('Test Project');
        $current_project_form_field = $current_project_form->findElement(WebDriverBy::cssSelector("#project_descr"));
        $current_project_form_field->click();
        $this->webDriver->getKeyboard()->sendKeys('Test Project description');
        sleep(1);
        $current_project_form->findElement(WebDriverBy::cssSelector(".project_buttons .save_button"))->click();
        sleep(1);
        $this->waitForUserInput('canCreateProject check...');
        try{
//            $this->webDriver->findElement(WebDriverBy::cssSelector(".project_crud"));
            $this->webDriver->findElement(WebDriverBy::xpath("//div[text()='Test Project']"));
        }catch (NoSuchElementException $e){
            $this->fail('No such a project created');
        }
    }
    private function canSeeAddButton(){
//        $this->waitForUserInput('canSeeAddButton...');
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