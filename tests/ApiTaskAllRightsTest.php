<?php
class ApiTaskAllRightsTest extends PHPUnit_Framework_TestCase {

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
        $this->current_user = $m;
        $app->addMethod('currentUser',function($user){return $this->current_user;});

        return $m;
    }

    /**
     * Add newly created user all permission for tasks.
     *
     * @depends testAddApp
     * @depends testCreateUser
     */
    public function testCreatePermissions(App_CLI $app, Model_User $user)
    {
        $this->app = $app;

        $m = $app->add('Model_Mock_User_Right');
        $m
            ->set('user_id',$user['id'])
            ->set('right','can_see_tasks,can_see_projects,can_see_quotes,can_add_task,can_edit_task,can_delete_task,can_add_comment_to_task')
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
        $m['name'] ='TestProject_ApiTaskAllRightsTest_'.$hash;
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
    public function testCreateTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success, Model_Project $project,
        Model_Quote $quote, Model_Requirement $requirement
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/task/saveParams&lhash='.$login_res_success->hash->lhash;
        $data = [
            'name'       => 'TestTask_ApiTaskAllRightsTest_'.$hash,
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
        ];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after creating a Task');
        $this->assertTrue(is_string($obj->result),'Task. Result was converted not to string by json_encode()');

        $this->assertEquals($obj->result,'success','Result of creating a Task is not successful.');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after creating a Task');
        $this->assertTrue(is_a($obj->data,'stdClass'),'Data is not an object of class stdClass after convertation of API respond on creating a Task');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data,'Task. Returned data form API doesn\'t have ID');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testApiLogin
     * @depends testCreateTask
     */
    public function testGetTask(
        App_CLI $app, $user_login_res, $create_object_res
    ) {
        $this->app = $app;

        $url = 'v1/task/getById&id='.$create_object_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after getting a Task');
        $this->assertTrue(is_string($obj->result),'Task. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of getting a Task is not successful');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after getting a Task');
        $this->assertTrue(is_array($obj->data),'Data is not an array after convertation of API respond on getting a Task');

        // obj :: data[0]
        $this->assertTrue(isset($obj->data[0]),'Data do not contain Task');
        $this->assertTrue( (count($obj->data)==1),'There is more then one Task in API respond on getting a Task by ID');
        $this->assertTrue(is_a($obj->data[0],'stdClass'),'Data[0] is not an object of class stdClass after convertation of API respond on getting a Task by ID');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data[0],'Task. Returned data form API doesn\'t have ID');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testGetTask
     */
    public function testUpdateTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $task_create_res
    ) {
        $this->app = $app;

        $hash = time();
        $new_name = 'TestTask_'.$hash.'_Updated_'.$hash;
        $url = 'v1/task/saveParams&id='.$task_create_res->data[0]->id.'&lhash='.$user_login_res->hash->lhash;
        $data = ['name' => $new_name];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after updating a Task');
        $this->assertTrue(is_string($obj->result),'Task. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of updating a Task is not successful');

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
     * @depends testGetTask
     * @depends testUpdateTask
     */
    public function testDeleteTask(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $task_create_res, $task_update_res
    ) {
        $this->app = $app;

        $url = 'v1/task/deleteById&id='.$task_create_res->data[0]->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after deleting a Task');
        $this->assertTrue(is_string($obj->result),'Task. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of deleting a Task is not successful');

        // obj :: deleted_record_id
        $this->assertObjectHasAttribute('deleted_record_id',$obj,'No deleted_record_id was returned form API after deleting a Task');


        // try if Task was SOFT deleted
        $pr = $this->app->add('Model_Task')->load($task_create_res->data[0]->id);
        $this->assertTrue($pr['is_deleted']==1,'Task SOFT delete is not working properly');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @ depends testApiLogin
     * @depends testCreateProject
     * @depends testCreateQuote
     * @depends testCreateRequirement
     * @depends testCreateTask
     * @depends testGetTask
     * @depends testUpdateTask
     * @depends testDeleteTask
     */
    public function testCleanDB(
        App_CLI $app, Model_User $user, Model_User_Right $rights, /*$login_res,*/
        Model_Project $test_project, Model_Quote $test_quote, Model_Requirement $test_requirement,
        $create_task_res, $get_task_res, $update_task_res, $del_task_res
    ) {

        $this->app = $app;

        $task_id = $create_task_res->data->id;
        $app->add('Model_Task')->load($task_id)->forceDelete();

        $test_requirement->forceDelete();
        $test_quote->forceDelete();
        $test_project->forceDelete();
        $user->forceDelete();
        $rights->delete();
    }

}