<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 19/12/14
 * Time: 17:41
 */
trait GetUserTrait {

    use Trait_Temp_Post;
//    use Trait_Temp_Proxy;

    public $current_user;
//    public $config;

    private function addApp()
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
    public function getUser()
    {
        $app = $this->addApp();
//        $this->config = new Config();

        $this->current_user = $app->add('Model_Mock_User');
        $this->current_user->loadBy('email',$this->config->current_user_email);

        return $this->current_user;
    }

}