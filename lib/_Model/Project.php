<?php
class Model_Project extends Model_Project_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
    function forClient() {
        $this->addCondition('client_id',$this->app->currentUser()->get('client_id'));
        return $this;
    }

    // TODO refactor this. Maybe join?
    function participateIn() {
        $participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->app->currentUser()->get('id'));
        $projects_ids="";
        foreach($participated_in->getRows() as $p){
            if($projects_ids=="") $projects_ids=$p['project_id'];
            else $projects_ids=$projects_ids.','.$p['project_id'];
        }
        $this->addCondition('id','in',$projects_ids);
        return $this;
    }
    function forDeveloper() {
        return $this->participateIn();
    }
    function forManager(){
        return $this;
    }
    function getAllParticipants($project_id){
        if ($project_id>0){
            $this->tryLoad($project_id);
        }

        // Get all managers from our organisation
        $managers=$this->add('Model_User');
        $managers->addCondition('is_manager',true);
        $par_ids=array();
        foreach($managers->getRows() as $u){
                if(!in_array($u['id'],$par_ids)) $par_ids[]=$u['id'];
        }

        if ($project_id>0){
            // Get all developers by project
            $mp=$this->add('Model_Participant');
            $mp->addCondition('project_id',$project_id);
            foreach($mp->getRows() as $u){
                if(!in_array($u['user_id'],$par_ids)) $par_ids[]=$u['user_id'];
            }

            // Get all clients by project
            $mu=$this->add('Model_User');
            $mu->addCondition('client_id',$this->get('client_id'));
            foreach($mu->getRows() as $u){
                if(!in_array($u['id'],$par_ids)) $par_ids[]=$u['id'];
            }
        }

        return $par_ids;
    }
}
