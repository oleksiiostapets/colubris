<?php
class endpoint_v1_project extends Endpoint_v1_General {

    public $model_class = 'Project';

    function init() {
        parent::init();
        $this->model->addCondition('is_deleted',false);
    }

    function get_getParticipants() {
        $id = $this->checkGetParameter('id');
        $mp = $this->add('Model_Participant');
        $mp->addCondition('project_id',$id);
        if($this->getParameter('name')){
            $name = $this->getParameter('name');
            $mp->addCondition('user','LIKE',$name.'%');
        }
        try{
            $data = $mp->getRows();
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

}