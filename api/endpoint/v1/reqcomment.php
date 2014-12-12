<?php
class endpoint_v1_reqcomment extends Endpoint_v1_General {

    public $model_class = 'Reqcomment';

    function init() {
        parent::init();
    }

    function post_saveParams(){
        $data_arr = $this->getFancyPost();

        if (is_array($data_arr)) {
            $all = array_merge($_REQUEST,$data_arr);
        } else {
            $all = $_REQUEST;
        }
        $id = $this->getId();
        $this->model->set($all);
        $this->model->set('user_id',$this->app->current_user->id);
        try{
            $this->model->save();
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

}