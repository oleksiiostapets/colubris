<?php
class endpoint_v1_task extends Endpoint_v1_General {

    public $model_class = 'Task';
    protected $required_fields = ['name','requirement_id'];

    function init() {
        parent::init();
        $this->model->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
    }

    function get_getStatuses(){
        $data = array();

        try{
            foreach ($this->model->task_statuses as $status){
                $data[] = array('id' => $status, 'name' => $status);
            }
            return[
                'result' => 'success',
                'data'   => $data
            ];
        }catch (Exception $e){
            return[
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }
    }
}