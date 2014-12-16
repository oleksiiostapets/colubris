<?php
class ApiQuoteDeleteRightTest extends PHPUnit_Framework_TestCase {

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
     * Add newly created user some permission to delete quotes only.
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
            ->set('right','can_delete_quote')
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
    public function testDeleteQuote(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, Model_Project $project
    ) {
        $this->app = $app;

        // create quote
        $hash = time();
        $q = $app->add('Model_Quote');
        $q
            ->set('name','TestQuote_ApiQuoteDeleteRightTest_'.$hash)
            ->set('project_id', $project->id)
            ->save()
        ;

        $url = 'v1/quote/deleteById&id='.$q->id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after deleting a quote');
        $this->assertTrue(is_string($obj->result),'quote. Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'success','Result of deleting a quote is not successful');

        // obj :: deleted_record_id
        $this->assertObjectHasAttribute('deleted_record_id',$obj,'No deleted_record_id was returned form API after deleting a quote');


        // try if quote was SOFT deleted
        $pr = $this->app->add('Model_Quote')->load($q->id);
        $this->assertTrue($pr['is_deleted']==1,'Quote SOFT delete is not working properly');

        //restore deleted
        $pr->set('is_deleted',0)->save();

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testDeleteQuote
     */
    public function testUpdateQuote(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_login_res, $quote
    ) {
        $this->app = $app;

        $hash = time();
        $new_name = 'TestQuote_'.$hash.'_Updated_'.$hash;
        $url = 'v1/quote/saveParams&id='.$quote->deleted_record_id.'&lhash='.$user_login_res->hash->lhash;
        $data = ['name' => $new_name];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after updating a quote');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after updating a quote');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5312','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after updating a quote');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     */
    public function testCreateQuote(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/quote/saveParams&lhash='.$login_res_success->hash->lhash;
        $data = [
            'name' => 'TestQuote_ApiQuoteCreateRightTest_'.$hash
        ];
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after creating a quote');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after creating a quote');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5311','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after creating a quote');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }

    /**
     * @depends testAddApp
     * @depends testApiLogin
     * @depends testDeleteQuote
     */
    public function testGetQuote(
        App_CLI $app, $user_login_res, $quote_res
    ) {
        $this->app = $app;
        $this->assertObjectHasAttribute('deleted_record_id',$quote_res,'Saved quote doesn\'t have ID');
        $this->assertTrue(!is_null($quote_res->deleted_record_id),'Saved quote doesn\'t have ID');

        $url = 'v1/quote/getById&id='.$quote_res->deleted_record_id.'&lhash='.$user_login_res->hash->lhash;
        $obj = json_decode($this->do_get_request($url));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after getting a quote');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after getting a quote');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5310','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after updating a quote');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }


    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @ depends testApiLogin
     * @ depends testCreateQuote
     * @ depends testUpdateQuote
     * @depends testDeleteQuote
     * @depends testCreateProject
     */
    public function testCleanDB(
        App_CLI $app, Model_User $user, Model_User_Right $rights
        /*, $login_res_obj, $quote, $create_quote_res_obj*/,$delete_quote_res_obj, Model_Project $project
    ) {
        $app->add('Model_Quote')->load($delete_quote_res_obj->deleted_record_id)->forceDelete();
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