<?php
class Model_Project_Base extends Model_Project_Definitions {
    function init(){
        parent::init();

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
    }

}
