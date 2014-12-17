<?php
class endpoint_v1_time extends Endpoint_v1_General {

    public $model_class = 'TaskTime';
    protected $required_fields = ['user_id','task_id','spent_time'];

    function init() {
        parent::init();
    }
}