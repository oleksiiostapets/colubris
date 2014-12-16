<?php
class ApiUserSeeRightTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Post;
    use Trait_Temp_Proxy;

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
        ;;

        return $app;
    }

    /**
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
            ->set('is_admin','1')
            ->save()
        ;
        $this->current_user = $m;
        $app->addMethod('currentUser',function($user){return $this->current_user;});
        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     */
    public function testCreatePermissions(App_CLI $app, Model_User $user)
    {
        $this->app = $app;
        $this->user = $user;

        $m = $app->add('Model_Mock_User_Right');
        $m
            ->set('user_id',$this->user['id'])
            ->set('right','can_see_users')
            ->save()
        ;
        return $m;
    }

    /**
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
    public function testApiCreateUser(App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res_success)
    {
        $this->app = $app;
        $url = 'v1/user/saveParams&lhash='.$login_res_success->hash->lhash;
        $data = array('name'=>'TestUser2_'.time(),'email'=>'email2_'.time().'@test.com','p'=>'123123');
        $obj = json_decode($this->do_post_request($url,$data));

        // obj :: result
        $this->assertObjectHasAttribute('result',$obj,'No result is returned form API after creating a user');
        $this->assertTrue(is_string($obj->result),'Result was converted not to string by json_encode()');
        $this->assertEquals($obj->result,'error','Result of request has unexpected "result" value');

        // obj :: code
        $this->assertObjectHasAttribute('code',$obj,'No code is returned form API after creating a user');
        $this->assertTrue(is_string($obj->code),'Code was converted not to string by json_encode()');
        $this->assertEquals($obj->code,'5311','Result of request has unexpected "code" value');

        // obj :: message
        $this->assertObjectHasAttribute('message',$obj,'No message is returned form API after creating a user');
        $this->assertTrue(is_string($obj->message),'Message was converted not to string by json_encode()');

        return $obj;
    }


    // TODO test for deleting user with no rights to delete


    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testApiCreateUser
     */
    public function testCleanDB(
        App_CLI $app, Model_User $user, Model_User_Right $rights,
        $res_success, $res_create
    ) {
        
        $this->app = $app;

        $rights->delete();
        $user->forceDelete();
        if (is_object($res_create)) {
            if (isset($res_create->data) && isset($res_create->data->id)) {
                $user->load($res_create->data->id);
                $user->forceDelete();
            }
        }
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