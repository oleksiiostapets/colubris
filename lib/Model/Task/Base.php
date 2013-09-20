<?php
class Model_Task_Base extends Model_Task_Definitions {
    public $table='task';

    function init(){
        parent::init();

        $this->addField('requester_id')->refModel('Model_User_Organisation');
        $this->addField('assigned_id')->refModel('Model_User_Organisation');
    }
}
