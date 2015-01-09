<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 *
 * User story:
 * - Create test User
 * - Set no rights at all
 * - Login to colubris
 * - Go to project page [cannot see this page]
 * - Add rights to see projects and reload page
 * - Check if add project button is hidden
 * - Give user rights to add projects
 * - Check if add project button is visible
 * - Try to add project again [success]
 * - Try to forceDelete user with existing project [esception]
 * - Try to edit created project [exception]
 * - Give user rights to edit project
 * - Try to edit project again [success]
 * - Go to quote page [cannot see this page]
 * - Try to add quotation [exception]
 * - Give user rights to add quotations
 * - Try to add new quote which belongs to newly created project [success]
 *
 * - Try to edit created quote [exception]
 * - Give user rights to edit quotes
 * - Try to edit quote [success]
 *
 * - Try to delete quote [exception]
 * - Give user rights to delete quote
 * - Try to delete quote [success]
 * - Check if quote was SOFT deleted
 * - Completely remove quote (forceDelete) from DB
 *
 * - Try to delete project [exception] <-
 * - Give user rights to delete project <-
 * - Try to delete project [success] <-
 * - Check if project was SOFT deleted <-
 * - Completely remove project (forceDelete) from DB <-
 *
 * - Delete user
 */
class UIUserStoryCreateQuoteTest extends PHPUnit_Framework_TestCase {

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

        $this->current_user_rights = $this->app->add('Model_User_Right');
    }

    public function tearDown() {
        if( $this->hasFailed() ) {
            $path = $this->config->screenshot_location . "/screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webDriver->takeScreenshot( $path );
        }
        $this->webDriver->close();
        $this->tryDeleteUser($this->current_user);
        if ($this->current_user_rights->loaded()) {
            $this->current_user_rights->delete();
        }
    }



    public function testAddProjectAndQuote() {

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


        // account
        $this->goToProjectPage();
        $this->userCannotSeeProjectPage();
        $this->addRight(['can_see_projects']);
        $this->goToProjectPage();
        $this->checkSeeProjectElements(true); // add button must be hidden
        $this->addRight(['can_see_projects','can_add_projects']);
        $this->goToProjectPage();
        $this->checkSeeProjectElements(); // add button must be visible

        // add project
        $this->clickAddProjectButton(); // add button must be visible
        $this->fillAddProjectForm();

        // No data can be saved to DB after executing this line
        // Video with presentation of this bug was created and shared on youtube
        // Link to video:
        // TODO check this line after fixing toolkit
        //$this->tryDeleteUser($this->current_user,'Exception_DB'); // user has project connected so cannot be force deleted

        // edit project
        $this->editProject(true); // user has no rights to edit project
        $this->addRight(['can_see_projects','can_edit_projects']);
        $this->goToProjectPage();
        $this->editProject(); // now user has rights to edit projects

        // add quote
        $this->addRight(['can_see_projects','can_edit_projects']);
        $this->goToProjectPage();
        $this->addQuote(true); // user has no right to edit add quote
        $this->addRight(['can_see_projects','can_edit_projects','can_see_quotes']);
        $this->goToProjectPage();
        $this->addQuote(true); // now user has right to see quote

        $this->addRight(['can_see_projects','can_edit_projects','can_see_quotes','can_add_quote']);
        $this->goToProjectPage();
        $this->addQuote(); // now user has right to add quote

        // edit quote

        // delete quote
        $this->goToProjectPage();
        $this->deleteQuote(true);
        $this->addRight(['can_see_projects','can_edit_projects','can_see_quotes','can_add_quote','can_delete_quote']);
        $this->goToProjectPage();
        $this->deleteQuote();

        // delete project
        $this->deleteProject(true); // user has no rights to delete projects
        $this->addRight(['can_see_projects','can_delete_projects']);
        $this->goToProjectPage();
        $this->deleteProject(); // now user has rights to delete projects

        $this->waitForUserInput('All pages tested! ');
    }

    protected function goToProjectPage() {
        $this->webDriver->get($this->config->test_url.'?page=projects');
        $this->waitForUserInput('Wait until Angular loads all templates.');
    }

    protected function userCannotSeeProjectPage() {
        $error_message = $this->webDriver->findElement(WebDriverBy::cssSelector("h2.cannot-see-page"));
        $this->assertTrue($error_message->getText()=='You cannot see this page','User with no rights can see projects');
        $this->assertContains('You cannot see this page', $this->webDriver->getTitle());
    }

    protected function addRight(array $rights) {
        $this->current_user_rights->setRights($this->current_user->id,$rights);
    }

    protected function checkSeeProjectElements($hidden=false) {
        $page_title         = $this->webDriver->findElement(WebDriverBy::xpath(".//h2[text()='Projects']"));
        $add_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.add_project_button"));

        $this->assertTrue($page_title->getText()=='Projects','Project page title is not correct.');
        $this->assertTrue($add_project_button->getAttribute('ng-click')=='Project.showForm()','Ng-click action on add new project button is not as expected.');
        if ($hidden) {
            $this->assertFalse($add_project_button->isDisplayed(),'User has no right to add project but he can see add project button');
        } else {
            $this->assertTrue($add_project_button->isDisplayed(),'User has right to add project but he cannot see add project button');
        }
    }

    protected function clickAddProjectButton() {

        // find the form wrapper
        $add_project_form_wrapper = $this->webDriver->findElement(WebDriverBy::cssSelector("div.floating-form.project_frame"));

        // form must be hidden
        $this->assertFalse($add_project_form_wrapper->isDisplayed());

        // click the add project button
        $add_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.add_project_button"));
        $add_project_button->click();

        // form must be visible
        $this->assertTrue($add_project_form_wrapper->isDisplayed());
    }

    protected function fillAddProjectForm() {

        // set project name
        $project_name_field = $this->webDriver->findElement(WebDriverBy::cssSelector("input#project_name"));
        $project_name_field->click();
        $this->webDriver->getKeyboard()->sendKeys('This is first test project');

        // set project description
        $project_description_field = $this->webDriver->findElement(WebDriverBy::cssSelector("textarea#project_descr"));
        $project_description_field->click();
        $this->webDriver->getKeyboard()->sendKeys('This is first test project description');

        // TODO: Add tests to check required fields

        // save project
        $save_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.save_button"));
        $save_project_button->click();

        $this->waitForUserInput('Wait for project saving. ');

    }

    protected function deleteProject($hidden=false) {

        // fing delete button. As we added only one projet this button is only one on the page.
        $delete_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.delete_button"));

        if ($hidden) {
            // delete button must be hidden
            $this->assertFalse($delete_project_button->isDisplayed());
        } else {
            // delete button must be visible
            $this->assertTrue($delete_project_button->isDisplayed());
            $delete_project_button->click();
            $this->waitForUserInput('Check message on confirm window.');
            $this->webDriver->switchTo()->alert()->accept();
        }

    }

    protected function tryDeleteUser(Model_User $user,$exception=false,$message=false) {
        try {
            $user->forceDelete();
            if ($exception) $this->fail('Expected exception was not thrown');
        } catch (Exception $e) {
            if ($exception) {
                $this->assertEquals(get_class($e),$exception);
                if ($message) {
                    $this->assertEquals($e->getMessage(),$message);
                }
            } else {
                $this->fail('Cannot delete user with ID ['. $user->id .'].');
            }
        }
    }

    protected function editProject($hidden=false) {

        // fing edit button. As we added only one projet this button is only one on the page.
        $edit_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.edit_button"));

        if ($hidden) {
            // delete button must be hidden
            $this->assertFalse($edit_project_button->isDisplayed());
        } else {
            // edit button must be visible
            $this->assertTrue($edit_project_button->isDisplayed());
            $edit_project_button->click();

            // set new project name
            $project_name_field = $this->webDriver->findElement(WebDriverBy::cssSelector("input#project_name"));
            $project_name_field->click();
            $this->webDriver->getKeyboard()->sendKeys(' UPDATED');

            // set new project description
            $project_description_field = $this->webDriver->findElement(WebDriverBy::cssSelector("textarea#project_descr"));
            $project_description_field->click();
            $this->webDriver->getKeyboard()->sendKeys(' UPDATED');

            // TODO: Add tests to check required fields

            // save project
            $save_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.save_button"));
            $save_project_button->click();

            $this->waitForUserInput('Wait for project saving. ');
        }

    }

    protected function addQuote($hidden=false) {

        // fing edit button. As we added only one projet this button is only one on the page.
        $edit_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.edit_button"));

        // edit button must be visible
        $this->assertTrue($edit_project_button->isDisplayed());
        $edit_project_button->click();

        // find add quote form
        $add_quote_form_wrapper = $this->webDriver->findElement(WebDriverBy::cssSelector("div#add-quote-wrapper"));


        if ($hidden) {
            // add quote form must be hidden
            $this->assertFalse($add_quote_form_wrapper->isDisplayed());
        } else {

            $name = 'Test quote' . time();

            $this->assertTrue($add_quote_form_wrapper->isDisplayed());

            // set new quote name
            $quote_name_field = $this->webDriver->findElement(WebDriverBy::cssSelector("input#quote_name"));
            $quote_name_field->click();
            $this->webDriver->getKeyboard()->sendKeys($name);

            // set new quote description
            $quote_description_field = $this->webDriver->findElement(WebDriverBy::cssSelector("textarea#quote_description"));
            $quote_description_field->click();
            $this->webDriver->getKeyboard()->sendKeys(' Test quote description');

            // TODO: Add tests to check required fields

            // save quote
            $save_quote_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.add_quote_button"));
            $save_quote_button->click();

            $this->waitForUserInput('Wait until newly created quota will appear above the form... ');

            $quotation_name = $this->webDriver->findElement(WebDriverBy::cssSelector("a.quote_name"));
            $this->assertEquals($name, $quotation_name->getText());

        }

        $this->waitForUserInput('Wait... ');
    }

    protected function editQuote() {

    }

    protected function deleteQuote($hidden=false) {

        // fing edit button. As we added only one projet this button is only one on the page.
        $edit_project_button = $this->webDriver->findElement(WebDriverBy::cssSelector("button.edit_button"));

        // edit button must be visible
        $this->assertTrue($edit_project_button->isDisplayed());
        $edit_project_button->click();

        $this->waitForUserInput('Wait until Angular loads all templates.');

        // find delete icon
        $quote_delete_icon = $this->webDriver->findElement(WebDriverBy::cssSelector("div#delete-quote-button-1"));

        if ($hidden) {
            // delete quote form must be hidden
            $this->assertFalse($quote_delete_icon->isDisplayed());
        } else {
            $this->assertTrue($quote_delete_icon->isDisplayed());

            // try delete quote
            $quote_delete_icon->click();
        }

        $this->waitForUserInput('Wait... ');
    }

}



//$this->sendConsoleMessage(count($old_errors_messages));

/*
            echo get_class($e)."\n";
            echo $e->getMessage()."\n";
            echo $e->getFile()."\n";
            echo $e->getLine()."\n";
            echo $e->getTraceAsString();
 */