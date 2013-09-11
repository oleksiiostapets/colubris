<?php
class View_TasksNew extends View {
    function init(){
        parent::init();

        if ($this->api->recall('project_id')>0){
	    	$pm=$this->add('Model_Project')->tryLoad($this->api->recall('project_id'));
	    	$this->add('P')->addClass('red_color')->setHtml('<strong>Project:</strong> '.$pm->get('name'));
    	}else $this->add('P')->addClass('red_color')->setHtml('<strong>Project:</strong> ---');
    	
        if ($this->api->recall('quote_id')>0){
	    	$pm=$this->add('Model_Quote')->tryLoad($this->api->recall('quote_id'));
	    	$this->add('P')->addClass('red_color')->setHtml('<strong>Quote:</strong> '.$pm->get('name'));
    	}else $this->add('P')->addClass('red_color')->setHtml('<strong>Quote:</strong> ---');
    	
        if ($this->api->recall('requirement_id')>0){
	    	$pm=$this->add('Model_Requirement')->tryLoad($this->api->recall('requirement_id'));
	    	$this->add('P')->addClass('red_color')->setHtml('<strong>Requirement:</strong> '.$pm->get('name'));
    	}else $this->add('P')->addClass('red_color')->setHtml('<strong>Requirement:</strong> ---');
    	
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

        	$this->js()->univ()->redirect($this->redirect_to)->execute();
        }
    }
}
