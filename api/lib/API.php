<?php
/**
 * Created by Vadym Radvansky
 * Date: 8/4/14 4:24 PM
 *
 * REST
 * http://book.agiletoolkit.org/app/rest.html
 *
 * autentification
 * https://github.com/atk4/agiletoolkit.org/blob/master/api/lib/Api2.php
 */
class API extends App_REST {

    public $current_user;

    function init(){
        parent::init();

        $this->dbConnect();
        $this->addPathfinder();
        $this->addRouter();
        $this->addAuth();

    }
    function currentUser() {
        return $this->current_user;
    }
    protected function addPathfinder() {
        $this->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib'),
            'mail'=>array('templates/mail'),
        ))->setBasePath('..');

    }
    protected function addRouter() {
        $router = $this->add('Controller_PatternRouter');
        $router
            ->link('v1/taskcomment',array('method','field_name','field_value'))
            ->link('v1/requirement',array('method','field_name','field_value'))
            ->link('v1/auth',array('method'))
            ->link('v1/account',array('method'))
            ->link('v1/project',array('method'))
            ->link('v1/user',array('method'))
            ->link('v1/task',array('method'))
            ->link('v1/client',array('method'))
            ->link('v1/quote')
        ;
        $router->route();
    }
    protected function addAuth() {
		$mu = $this->add('Model_User')->notDeleted();
        $this->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel($mu, 'email', 'password')
        ;

//        if(!$this->auth->model['id']){
//            $hash = $this->hg_cookie->getLoginHash();
//            if($hash){
//                $u = $this->add('Model_User')->tryLoadBy('hash',$hash);
//                if($u->loaded()){
//                    $this->auth->login($u["email"]);
//                }
//            }
//        }
//        $this->user_access->setUser($this->currentUser());
    }
}















        // ---------------------------------------------------
        //
        //                    PATHFINDER
        //
        // ---------------------------------------------------
