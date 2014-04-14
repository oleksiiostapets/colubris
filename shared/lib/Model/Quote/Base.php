<?php
class Model_Quote_Base extends Model_Quote_Definitions {
    function init(){
        parent::init(); //$this->debug();

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
    }
}