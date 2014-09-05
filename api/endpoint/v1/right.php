<?php
class endpoint_v1_right extends Endpoint_v1_General {

    public $model_class = 'User_Right';

    function init() {
        parent::init();
    }

    function get_getAvailableRights(){
        try{

            return [
                'result' => 'success',
                'data'   => Model_User_Right::$available_rights,
            ];
        }catch(Exception $e){
            return [
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }

    function post_setRights(){

        try{
            $data_arr = $this->getFancyPost();
            $id = $this->getId();
            $this->model->setRights($id,$data_arr);
            return [
                'result' => 'success',
                'data' => $this->model->get(),
            ];
        }catch(Exception $e){
            return [
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }

    function get_getCurrentUserRights(){
        try{
            $mr = $this->add('Model_User_Right');
            $mr->addCondition('user_id', $this->app->currentUser()->get('id'));
            $mr->tryLoadAny();

            return [
                'result' => 'success',
                'data'   => $mr->get('right'),
            ];
        }catch(Exception $e){
            return [
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }
}