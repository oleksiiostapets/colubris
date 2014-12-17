<?php
class ApiCommentToRequirementRightTest extends PHPUnit_Framework_TestCase {

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
     * Add newly created user all permission for Comments.
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
            ->set('right','can_add_comment_to_requirement')
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
        $m['name'] ='TestProject_ApiCommentToRequirementRightTest_'.$hash;
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
        $m['name'] = 'TestQuote_ApiCommentToRequirementRightTest_'.$hash;
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
        $m['name'] = 'TestRequirement_ApiCommentToRequirementRightTest_'.$hash;
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
     * @depends testCreateRequirement
     */
    public function testCreateComment(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success, Model_Requirement $requirement
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/reqcomment/saveParams&lhash='.$login_res_success->hash->lhash;
        $data = [
            'requirement_id' => $requirement->id,
            'text'           => 'TestComment_ApiCommentToRequirementRightTest_'.$hash,
        ];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after creating a Comment');
        $this->assertTrue(is_string($obj->result),'Comment. Result was converted not to string by json_encode()');

        $this->assertEquals($obj->result,'success','Result of creating a Comment is not successful.');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after creating a Comment');
        $this->assertTrue(is_a($obj->data,'stdClass'),'Data is not an object of class stdClass after convertation of API respond on creating a Comment');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data,'Comment. Returned data form API doesn\'t have ID');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testApiLogin
     * @depends testCreateComment
     */
    public function testGetComment(
        App_CLI $app, $user_login_res, $comment_res
    ) {
        $this->app = $app;

        $url = 'v1/reqcomment/getById&id='.$comment_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after getting a Comment');
        $this->assertTrue(is_string($obj->result),'Comment. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of getting a Comment is not successful');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after getting a Comment');
        $this->assertTrue(is_array($obj->data),'Data is not an array after convertation of API respond on getting a Comment');

        // obj :: data[0]
        $this->assertTrue(isset($obj->data[0]),'Data do not contain Comment');
        $this->assertTrue( (count($obj->data)==1),'There is more then one Comment in API respond on getting a Comment by ID');
        $this->assertTrue(is_a($obj->data[0],'stdClass'),'Data[0] is not an object of class stdClass after convertation of API respond on getting a Comment by ID');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data[0],'Comment. Returned data form API doesn\'t have ID');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateComment
     */
    public function testUpdateComment(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $comment_res
    ) {
        $this->app = $app;

        $hash = time();
        $new_name = 'TestComment_ApiCommentToRequirementRightTest_'.$hash.'_Updated';
        $url = 'v1/reqcomment/saveParams&id='.$comment_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $data = [
            'text' => $new_name,
        ];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after updating a Comment');
        $this->assertTrue(is_string($obj->result),'Comment. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of updating a Comment is not successful');

        // obj :: data
        $this->assertObjectHasAttribute('data',$obj,'No data is returned form API after updating a Comment');
        $this->assertTrue(is_a($obj->data,'stdClass'),'Data is not an object of class stdClass after convertation of API respond on updating a Comment');

        // obj :: data :: id
        $this->assertObjectHasAttribute('id',$obj->data,'Comment. Returned data form API doesn\'t have ID');
        // obj :: data :: text
        $this->assertObjectHasAttribute('text',$obj->data,'Comment. Returned data form API doesn\'t have text field');
        $this->assertTrue( ($obj->data->text==$new_name) ,'Comment. Name returned by API doesn\'t match setting name');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testCreateComment
     */
    public function testDeleteComment(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $comment_res
    ) {
        $this->app = $app;

        $url = 'v1/reqcomment/deleteById&id='.$comment_res->data->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after deleting a Comment');
        $this->assertTrue(is_string($obj->result),'Comment. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of deleting a Comment is not successful');

        // obj :: deleted_record_id
        $this->assertObjectHasAttribute('deleted_record_id',$obj,'No deleted_record_id was returned form API after deleting a Comment');


        // try if comment was SOFT deleted
        $pr = $this->app->add('Model_Reqcomment')->load($comment_res->data->id);
        $this->assertTrue($pr['is_deleted']==1,'Comment SOFT delete is not working properly');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testCreateProject
     * @depends testCreateQuote
     * @depends testCreateRequirement
     * @depends testCreateComment
     */
    public function testCleanDB(
        App_CLI $app, Model_User $user, Model_User_Right $rights, Model_Project $test_project,
        Model_Quote $test_quote, Model_Requirement $test_requirement, $comment_res
    ) {

        $this->app = $app;

        $comment_id = $comment_res->data->id;
        $app->add('Model_Reqcomment')->load($comment_id)->forceDelete();

        $test_requirement->forceDelete();
        $test_quote->forceDelete();
        $test_project->forceDelete();
        $user->forceDelete();
        $rights->delete();
    }

}