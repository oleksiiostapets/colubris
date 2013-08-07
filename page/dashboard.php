<?php

class page_dashboard extends Page {
	function page_time(){
	
		if (!$_GET['task_id']) {
			throw $this->exception('task_id must be provided!');
		}
	
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
	
		if ($_GET['reload_view']) {
			$this->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
					$this->js(null,'function(event, ui){
                            '.$this->js()->_selector('#'.$_GET['reload_view'])->trigger('reload').'
                        }
                ')
			);
		}
		 
	}
	
	function page_attachments(){
	
		if (!$_GET['task_id']) {
			throw $this->exception('task_id must be provided!');
		}
	
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
	
		if ($_GET['reload_view']) {
			$this->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
					$this->js(null,'function(event, ui){
                            '.$this->js()->_selector('#'.$_GET['reload_view'])->trigger('reload').'
                        }
                ')
			);
		}
	}
	
}
