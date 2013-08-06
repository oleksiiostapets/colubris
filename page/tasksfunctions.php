<?php

class page_tasksfunctions extends Page {
    function page_time(){
    	$this->api->stickyGet('task_id');
      	$model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
       	$crud=$this->add('CRUD');
       	$crud->setModel($model,
       			array('spent_time','comment'),
       			array('user','spent_time','comment','created_dts')
       	);
       	if ($crud->grid){
       		$crud->add_button->setLabel('Add Time');
       	}
       		
    }
	
	function page_attachments(){
    	$this->api->stickyGet('task_id');
      	$model=$this->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
       	$crud=$this->add('CRUD');
       	$crud->setModel($model,
       			array('description','file_id'),
       			array('description','file','updated_dts')
       	);
       	if($crud->grid){
       		$crud->grid->addFormatter('file','download');
       	}
    }
	        
}
