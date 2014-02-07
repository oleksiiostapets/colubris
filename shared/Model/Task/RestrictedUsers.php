<?php
class Model_Task_RestrictedUsers extends Model_Task_Definitions {

    function init(){
        parent::init();

        $this->addCondition('is_deleted',false);

        $this->addField('requester_id')->refModel('Model_User_Task');
        $this->addField('assigned_id')->refModel('Model_User_Task');
    }
}
