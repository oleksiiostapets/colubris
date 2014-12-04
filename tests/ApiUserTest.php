<?php
class ApiUserTest extends PHPUnit_Framework_TestCase {

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
            ->set('right','can_see_users,can_manage_users')
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
        $res = json_decode($this->do_post_request($url,$data));

        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     */
    public function testApiCreateUser(App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res)
    {
        $this->app = $app;
        $url = 'v1/user/saveParams&lhash='.$login_res->hash->lhash;
        $data = array('name'=>'TestUser2_'.time(),'email'=>'email2_'.time().'@test.com','p'=>'123123');
        $res = json_decode($this->do_post_request($url,$data));

        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     */
    public function testRemovePermissions(App_CLI $app, Model_User $user, Model_Mock_User_Right $r)
    {
        $this->app = $app;
        $this->user = $user;

        $r
            ->set('right','')
            ->save()
        ;
        return $r;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     */
    public function testApiCreateUserWithoutPermissions(App_CLI $app, Model_User $user, Model_User_Right $rights, $login_res)
    {
        $this->app = $app;
        $url = 'v1/user/saveParams&lhash='.$login_res->hash->lhash;
        $data = array('name'=>'TestUser3_'.time(),'email'=>'email3_'.time().'@test.com','p'=>'123123');
        $res = json_decode($this->do_post_request($url,$data));

        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testApiCreateUser
     * @depends testRemovePermissions
     * @depends testApiCreateUserWithoutPermissions
     *
     * @ expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function testAssumptions(
        App_CLI $app, Model_User $user, Model_User_Right $rights,
        $res_success, $res_create,
        Model_User_Right $no_rights,
        $res_failure
    ) {
        $this->cleanDB($user, $rights, $res_create);

        $this->assertObjectHasAttribute('result',$res_success);
        $this->assertEquals($res_success->result,'success');
        $this->assertObjectHasAttribute('result',$res_create);
        $this->assertEquals($res_create->result,'success');
        $this->assertEquals($res_failure,NULL);
    }

    private function cleanDB(Model_User $user, Model_User_Right $rights, $res_create) {
        $rights->delete();
        $user->forceDelete();
        if (is_object($res_create)) {
            $user->load($res_create->data->id);
            $user->forceDelete();
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