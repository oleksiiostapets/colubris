<?php
class Model_Client_Base extends Model_Client_Definitions {
    function init(){
        parent::init();

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
    }
}
