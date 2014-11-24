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
    public function testCreateAdmin(App_CLI $app)
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
     * @depends testCreateAdmin
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
    }

    private function cleanDB(Model_User $user) {
        try {
            $user->forceDelete();
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getFile()."\n";
            echo $e->getLine()."\n";
            echo $e->getTraceAsString();

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