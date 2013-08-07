<?php
class View_TasksNew extends View {
    function init(){
        parent::init();

        if ($this->api->recall('project_id')>0){
	    	$pm=$this->add('Model_Project')->load($this->api->recall('project_id'));
	    	$this->add('P')->addClass('red_color')->setHtml('<strong>Project:</strong> '.$pm->get('name'));
    	}
    	
    	$m=$this->add('Model_Task');
    	$f=$this->add('Form');
    	$f->setModel($m,$this->fields);
    	
    	$f->addSubmit('Save');
    	
        if($f->isSubmitted()){
        	if ($this->api->recall('project_id')>0){
        		$f->getModel()->set('project_id',$this->api->recall('project_id'));
        	}
            if ($this->api->recall('requirement_id')>0){
        		$f->getModel()->set('requirement_id',$this->api->recall('requirement_id'));
        	}
        	$f->update();
        	
        	$to='';
        	if ($f->get('requester_id')>0){
        		$u=$this->add('Model_User')->load($f->get('requester_id'));
        		if ($u['email']!='') $to=$u['email'];
        	}
        	if ($f->get('assigned_id')>0){
        		$u=$this->add('Model_User')->load($f->get('assigned_id'));
        		if ($u['email']!=''){
        			if ($to=='') $to=$u['email']; else $to.=', '.$u['email'];
        		}
        	}
        	if ($to!=''){
        		$this->api->mailer->sendMail($to,'new_task',array('link'=>$this->api->url('/manager/tasks')));
        	}
        	 
            $this->js()->univ()->redirect($this->redirect_to)->execute();
        }
    }
}
