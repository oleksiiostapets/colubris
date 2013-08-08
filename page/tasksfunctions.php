<?php

class page_tasksfunctions extends Page {
    function page_time(){

        if (!$_GET['task_id']) {
            throw $this->exception('task_id must be provided!');
        }

    	$this->api->stickyGet('task_id');
      	$model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
       	$crud=$this->add('CRUD');
       	$crud->setModel($model,
       			array('spent_time','comment','date'),
       			array('user','spent_time','comment','date')
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
	/*
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
*/
    function page_more(){
        if (!$_GET['task_id']) {
            throw $this->exception('task_id must be provided!');
        }
    	$this->api->stickyGET('task_id');
    	$task=$this->add('Model_Task')->load($_GET['task_id']);
    	 
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->makeUrls($task->get('descr_original')));
    
    	$this->add('View')->setHtml('<hr /><strong>Attachments:</strong> ');

    	$model=$this->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
    	$crud=$this->add('CRUD');
    	$crud->setModel($model,
    			array('description','file_id'),
    			array('description','file','updated_dts')
    	);
    	if($crud->grid){
    		$crud->grid->addFormatter('file','download');
    	}
    	 
    	$this->add('View')->setHtml('<hr /><strong>Comments:</strong> ');
    
    	$cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));
    
    	$m=$this->add('Model_Taskcomment')
    			->addCondition('task_id',$_GET['task_id']);
    	$cr->setModel($m,
    			array('text','file_id'),
    			array('text','user','file','created_dts')
    	);
    	if($cr->grid){
    		$cr->add_button->setLabel('Add Comment');
    		$cr->grid->setFormatter('text','text');
    		$cr->grid->addFormatter('file','download');
    	}
    	if($_GET['delete']){
    		$comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
    		$comment->delete();
    		$cr->js()->reload()->execute();
    	}
    }
    
}
