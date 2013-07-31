<?php

class page_client_tasks_new extends Page {

    function initMainPage() {
    	$s=$this->add('View_Switcher');
    	
        if ($this->api->recall('project_id')>0){
	    	$pm=$this->add('Model_Project')->load($this->api->recall('project_id'));
	    	$this->add('P')->addClass('red_color')->setHtml('<strong>Project:</strong> '.$pm->get('name'));
    	}
    	
    	$m=$this->add('Model_Task');
    	$f=$this->add('Form');
    	$f->setModel($m,array('name','descr_original','estimate','priority'));
    	
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
            $this->js()->univ()->redirect('/client/tasks')->execute();
        }
    }
}
