<?php

class page_team_tasks_new extends Page {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
    	$m=$this->add('Model_Task');
    	$f=$this->add('Form');
    	$f->setModel($m,array('name','descr_original','estimate','priority','status'));
    	
    	$f->addSubmit('Save');
    	
        if($f->isSubmitted()){
        	if ($this->api->recall('project_id')>0){
        		$f->getModel()->set('project_id',$this->api->recall('project_id'));
        	}
            if ($this->api->recall('requirement_id')>0){
        		$f->getModel()->set('requirement_id',$this->api->recall('requirement_id'));
        	}
        	$f->getModel()->set('assigned_id',$this->api->auth->model['id']);
        	$f->update();
            $this->js()->univ()->redirect('/team/tasks')->execute();
        }
    }
}
