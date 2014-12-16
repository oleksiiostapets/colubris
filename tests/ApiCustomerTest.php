<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 21/11/14
 * Time: 23:46
 */
class ApiCustomerTest extends PHPUnit_Framework_TestCase {

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
    public function testApiLogin(App_CLI $app, Model_User $user)
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
     */
    public function testApiLoginFail(App_CLI $app, Model_User $user)
    {
        $this->app = $app;

        $url = 'v1/auth/login/';
        $data = array('u'=>$user['email'],'p'=>'123123123123');
        $res = json_decode($this->do_post_request($url,$data));

        return $res;
    }



    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testApiLogin
     * @depends testApiLoginFail
     *
     * @ expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function testAssumptions(
        App_CLI $app, Model_User $user,
        $res_success,
        $res_fail
    ) {
        $this->cleanDB($user);

        $this->assertEquals($res_success->result,'success');
        $this->assertTrue(isset($res_success->hash));
        $this->assertEquals($res_fail->result,'error');
        $this->assertFalse(isset($res_fail->hash));
    }





    private function cleanDB(Model_User $user) {
        $user->forceDelete();
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