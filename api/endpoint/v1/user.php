<?php
class endpoint_v1_user extends Endpoint_v1_General {

    public $model_class = 'User';

    function init() {
        parent::init();
    }
    public function get_getUsers(){
        $this->model->notDeleted();
        $data = $this->model->getRows();
        echo json_encode([
            'result' => 'success',
            'data'   => $data,
        ]);
        exit();
    }

    function post_login() {
        if(!$this->app->auth->verifyCredentials($_POST['u'],$_POST['p'])) return false; else {
            $u = $this->add('Model_User')->tryLoadBy('email',$_POST['u']);
            if($u->loaded()){
                $res = $u->setLHash();
            }else return false;
            //$res = $u->checkUserByLHash('72ffa947a251bec0e71887ad689a2bcf');
            return $res;
        }
    }

    function get_check(){
        $u = $this->add('Model_User');
        $res = $u->checkUserByLHash($_GET['lhash']);

        return $res;
    }
}