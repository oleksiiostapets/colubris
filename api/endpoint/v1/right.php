<?php
class endpoint_v1_right extends Endpoint_v1_General {

    public $model_class = 'User_Right';

    function init() {
        parent::init();
    }

    function get_getAvailableRights(){
        try{

            echo json_encode([
                'result' => 'success',
                'data'   => Model_User_Right::$available_rights,
            ]);
            exit();
        }catch(Exception $e){
            echo json_encode([
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ]);
            exit();
        }
    }

    function post_setRights(){

        try{
            $data_arr = $this->getFancyPost();
            $id = $this->getId();
            $this->model->setRights($id,$data_arr);
            echo json_encode([
                'result' => 'success',
                'data' => $this->model->get(),
            ]);
            exit();
        }catch(Exception $e){
            echo json_encode([
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ]);
            exit();
        }
    }
}