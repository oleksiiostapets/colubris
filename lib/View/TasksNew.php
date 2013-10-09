<?php
class View_TasksNew extends View {
    function init(){
        parent::init();

    	$m=$this->add('Model_Task_RestrictedUsers');
    	$f=$this->add('Form');
    	$f->setModel($m,$this->fields);
    	
    	$f->addSubmit('Save');
    	
        if($f->isSubmitted()){
        	if ($this->api->recall('project_id')>0){
        		$f->getModel()->set('project_id',$this->api->recall('project_id'));
        	}
            if ($this->api->recall('equirement_id')>0){
        		$f->getModel()->set('requirement_id',$this->api->recall('requirement_id'));
        	}
        	$f->update();

        	$this->js()->univ()->redirect($this->redirect_to)->execute();
        }
    }
}
