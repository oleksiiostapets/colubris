<?php
class endpoint_v1_quote extends Endpoint_v1_General {

    public $model_class = 'Quote';
    protected $required_fields = ['name','project_id'];

    function init() {
        parent::init();
    }
}