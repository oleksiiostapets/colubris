<?php
class Model_User_Task extends Model_User_Organisation {
    function init(){
        parent::init();

        $this->api->stickyGet('project_id');
        $p=$this->add('Model_Project');
        $par_ids=$p->getAllParticipants($_GET['project_id']);
        $this->addCondition('id','in',implode(',',$par_ids));
    }
}
