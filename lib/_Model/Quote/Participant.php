<?php
class Model_Quote_Participant extends Model_Quote {

    function init(){
        parent::init();

        $participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->api->auth->model['id']);
        $projects_ids="";
        foreach($participated_in as $p){
        	if($projects_ids=="") $projects_ids=$p['project_id'];
        	else $projects_ids=$projects_ids.','.$p['project_id'];
        }
        $this->addCondition('project_id','in',$projects_ids);
    }
    
}
