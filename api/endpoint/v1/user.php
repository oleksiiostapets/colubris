<?php
class endpoint_v1_user extends Endpoint_v1_General {

    public $model_class = 'User';

    function init() {
        parent::init();
    }
    public function get_getUsers(){
        $this->model->notDeleted()->getUsersOfOrganisation();
        try{
            $data = $this->model->prepareForSelect($this->app->current_user)->getRows();
            return [
                'result' => 'success',
                'data'   => $data,
            ];
        }catch (Exception $e){
            return[
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }
    public function get_getAllUsers(){
        $this->model->notDeleted()->getUsersOfOrganisation();
        try{
            $data = $this->model->getRows();
            return [
                'result' => 'success',
                'data'   => $data,
            ];
        }catch (Exception $e){
            return[
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }
    public function get_getUsersByProject(){
        $this->model->notDeleted()->getUsersOfOrganisation();
        $project_id = $this->getParameter('project_id');
        if($project_id){
            $r=$this->model->leftJoin('participant.user_id','id','left','_r');
            $r->addField('project_id','project_id');
            $this->model->addCondition('project_id',$project_id);
        }
        try{
            $data = $this->model->getRows();
            return [
                'result' => 'success',
                'data'   => $data,
            ];
        }catch (Exception $e){
            return[
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
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

    function post_saveUser(){
        $all = $_REQUEST;
        unset($all['lhash']);
        $id = $this->checkGetParameter('id',true);
        if ($id) {
            $this->model->tryLoad($id);
            if(!$this->model->loaded()){
                return [
                    'result' => 'error',
                    'error_message' => 'Record with the id '.$id.' was not found',
                ];
            }
        }else{
            $this->model->tryLoadBy('email',$_REQUEST['email']);
            if($this->model->loaded()){
                return [
                    'result' => 'validation_error',
                    'message' => 'User with this email already exists',
                ];
            }
            $this->model->set('organisation_id',$this->app->currentUser()->get('organisation_id'));
        }
        $this->model->set($all);
        $this->model->save();
        return [
            'result' => 'success',
            'data' => $this->model->get(),
        ];
    }
    function post_saveParams(){
        unset($_REQUEST['lhash']);
        $id = $this->checkGetParameter('id',true);
        if (!$id) {
            $this->model->tryLoadBy('email',$_REQUEST['email']);
            if($this->model->loaded()){
                return [
                    'result' => 'validation_error',
                    'message' => 'User with this email already exists',
                ];
            }
        }
        return parent::post_saveParams();
    }
}