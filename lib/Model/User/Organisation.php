<?php
class Model_User_Organisation extends Model_User {
    function init(){
        parent::init();

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
    }
}
