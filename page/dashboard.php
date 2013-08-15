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

	function page_more(){
		if (!$_GET['task_id']) {
			throw $this->exception('task_id must be provided!');
		}

        $this->api->stickyGET('task_id');
       	$task=$this->add('Model_Task')->load($_GET['task_id']);


        $v = $this->add('View');

        // Description
        $descr_view = $v->add('View')->addClass('span12');
        $descr_view->add('H3')->set('Description');
        $descr_view->add('View')->setHtml( $this->api->makeUrls($task->get('descr_original')) );

        // left view
        $left_view = $v->add('View')->setClass('span6 right');
        $left_view->add('H3')->set('Attachments');

       	$model=$left_view->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
       	$crud=$left_view->add('CRUD',array(
               'grid_class' => 'Grid_Attachments',
           ));
       	$crud->setModel($model,
       			array('description','file_id'),
       			array('description','file','file_thumb','updated_dts')
   //    			array()
       	);

           // right view
        $right_view = $v->add('View')->setClass('span6 left');
        $right_view->add('H3')->set('Comments');

       	$cr=$right_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

       	$m=$right_view->add('Model_Taskcomment')
       			->addCondition('task_id',$_GET['task_id']);
       	$cr->setModel($m,
       			array('text','file_id'),
       			array('text','user','file','file_thumb','created_dts')
   //    			array()
       	);
       	if($cr->grid){
       		$cr->add_button->setLabel('Add Comment');
       		//$cr->grid->setFormatter('text','text');
       		//$cr->grid->addFormatter('file','download');
       	}
       	if($_GET['delete']){
       		$comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
       		$comment->delete();
       		$cr->js()->reload()->execute();
       	}

	}
	
}
