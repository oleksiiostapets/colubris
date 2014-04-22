<?php
class Model_User_Task extends Model_User {
    function init(){
        parent::init();

		$this->getUsersOfOrganisation();

        /*if ($this->api->recall('project_id')){
            $p=$this->add('Model_Project')->notDeleted();
            $par_ids=$p->getAllParticipants($this->api->recall('project_id'));
            $this->addCondition('id','in',implode(',',$par_ids));
        }*/
        if ($_GET['project']){
            $p=$this->add('Model_Project')->notDeleted();
            $par_ids=$p->getAllParticipants($_GET['project_id']);
            $this->addCondition('id','in',implode(',',$par_ids));
        }
    }
}
