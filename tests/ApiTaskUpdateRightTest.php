<?php
class ApiTaskUpdateRightTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Post;
    use Trait_Temp_Proxy;

    /**
     * Creates AgileToolkit application to user it in other tests
     */
    public function testAddApp()
    {
        $app = new App_CLI();
        $app->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib','../lib'),
            'mail'=>array('templates/mail'),
        ))->setBasePath('.');
        $app->dbConnect();
        $app->page = '';
        $app->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User', 'email', 'password')
        ;
        $app->is_test_app = true;
        return $app;
    }

    /**
     * Creates test user to use it with other tests.
     * This user must be deleted after tests are finished.
     *
     * @depends testAddApp
     */
    public function testCreateUser(App_CLI $app)
    {
        $this->app = $app;

        $user_hash = time();
        $m = $app->add('Model_Mock_User');
        $m
            ->set('name','TestUser_'.$user_hash)
            ->set('email','tu_'.$user_hash)
            ->set('password','123123')
            ->save()
        ;

        return $m;
    }

    /**
     * Add newly created user some permission to update Tasks only.
     *
     * @depends testAddApp
     * @depends testCreateUser
     */
    public function testCreatePermissions(App_CLI $app, Model_User $user)
    {
        $this->app = $app;

        $m = $app->add('Model_Mock_User_Right');
        //$m->set = true;
        $m
            ->set('user_id',$user['id'])
            ->set('right','can_edit_task')
            ->save()
        ;

        return $m;
    }

    /**
     * Login to API with credentials of newly created user
     *
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     */
    public function testApiLogin(App_CLI $app, Model_User $user, Model_User_Right $rights)
    {
        $this->app = $app;

        $url = 'v1/auth/login/';
        $data = array('u'=>$user['email'],'p'=>'123123');
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after login');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of login is not successful');

        // obj :: hash
        $this->assertObjectHasAttribute('hash',$obj,'');
        $this->assertTrue(is_a($obj->hash,'stdClass'),'Hash is not an object of class stdClass after convertation of API respond on user login');

        // obj :: hash :: lhash
        $this->assertObjectHasAttribute('lhash',$obj->hash,'No lhash is returned form API after login');
        $this->assertTrue(is_string($obj->hash->lhash),'lhash was converted not to string by json_encode()');

        // obj :: hash :: lhash_exp
        $this->assertObjectHasAttribute('lhash_exp',$obj->hash,'No lhash_exp is returned form API after login');
        $this->assertTrue(is_string($obj->hash->lhash_exp),'lhash_exp was converted not to string by json_encode()');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     */
    public function testCreateProject(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success
    ) {
        $this->app = $app;

        $hash = time();
        $m = $app->add('Model_Project');
        $m['name'] ='TestProject_ApiProjectAllRightsTest_'.$hash;
        $m->save();

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$m,'Saved Project doesn\'t have ID');
        $this->assertTrue(!is_null($m->id),'Saved Project doesn\'t have ID');

        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateProject
     */
    public function testCreateQuote(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success, Model_Project $project
    ) {
        $this->app = $app;

        $hash = time();
        $m = $app->add('Model_Quote');
        $m['name'] = 'TestQuote_ApiTaskAllRightsTest_'.$hash;
        $m['project_id'] = $project->id;
        $m->save();

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$m,'Saved Project doesn\'t have ID');
        $this->assertTrue(!is_null($m->id),'Saved Project doesn\'t have ID');

        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateProject
     * @depends testCreateQuote
     */
    public function testCreateRequirement(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success, Model_Project $project,
        Model_Quote $quote
    ) {
        $this->app = $app;

        $hash = time();
        $m = $app->add('Model_Requirement');
        $m['name'] = 'TestRequirement_ApiTaskAllRightsTest_'.$hash;
        $m['quote_id'] = $quote->id;
        $m->save();

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$m,'Saved Project doesn\'t have ID');
        $this->assertTrue(!is_null($m->id),'Saved Project doesn\'t have ID');

        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateProject
     * @depends testCreateQuote
     * @depends testCreateRequirement
     */
    public function testUpdateTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, Model_Project $project,
        Model_Quote $quote, Model_Requirement $requirement
    ) {
        $this->app = $app;

        // create Task
        $hash = time();
        $q = $app->add('Model_Task');
        $q
            ->set('name','TestTask_ApiTaskUpdateRightTest_'.$hash)
            ->set('requirement_id', $requirement->id)
            ->save()
        ;

        $hash = time();
        $new_name = 'TestTask_'.$hash.'_Updated_'.$hash;
        $url = 'v1/task/saveParams&id='.$q->id.'&lhash='.$user_login_res->hash->lhash;
        $data = ['name' => $new_name];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after updating a Task');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of request has unexpected "result" value');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after updating a Task');
        $this->assertTrue(is_a($obj->data,'stdClass'),'Data is not an object of class stdClass after convertation of API respond on updating a Task');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data,'Task. Returned data form API doesn\'t have ID');
        // obj :: data :: name
        $this->assertObjectHasAttribute('name',$obj->data,'Task. Returned data form API doesn\'t have name field');
        $this->assertTrue( ($obj->data->name==$new_name) ,'Task. Name returned by API doesn\'t match setting name');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateQuote
     */
    public function testCreateTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success, Model_Quote $quote
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/task/saveParams&lhash='.$login_res_success->hash->lhash;
        $data = [
            'name'     => 'TestTask_ApiTaskUpdateRightTest_'.$hash,
            'quote_is' => $quote->id
        ];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after creating a Task');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after creating a Task');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5311','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after creating a Task');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testApiLogin
     * @depends testUpdateTask
     */
    public function testGetTask(
        App_CLI $app, $user_login_res, $task_res
    ) {
        $this->app = $app;
        $this->assertObjectHasAttribute('id',$task_res->data,'Saved Task doesn\'t have ID');
        $this->assertTrue(!is_null($task_res->data->id),'Saved Task doesn\'t have ID');

        $url = 'v1/task/getById&id='.$task_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after getting a Task');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after getting a Task');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5310','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after updating a Task');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateTask
     * @depends testUpdateTask
     */
    public function testDeleteTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $task, $task_update_res
    ) {
        $this->app = $app;

        $url = 'v1/task/deleteById&id='.$task_update_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after deleting a Task');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after deleting a Task');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5313','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after deleting a Task');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }


    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testCreateProject
     * @depends testCreateQuote
     * @depends testCreateRequirement
     * @depends testUpdateTask
     */
    public function testCleanDB(
        App_CLI $app, Model_User $user, Model_User_Right $rights, Model_Project $project, Model_Quote $quote,
        Model_Requirement $requirement, $update_task_res_obj
    ) {
        $app->add('Model_Task')->load($update_task_res_obj->data->id)->forceDelete();
        $requirement->forceDelete();
        $quote->forceDelete();
        $project->forceDelete();
        $rights->delete();
        $user->forceDelete();

        return true;
    }

}



/*


        try {
            $user->forceDelete();
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getFile()."\n";
            echo $e->getLine()."\n";
            echo $e->getTraceAsString();

        }


 */