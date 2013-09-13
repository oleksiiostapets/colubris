<?php

class page_dashboard extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeDashboard() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Dashboard',
                    'url' => 'dashboard',
                ),
            )
        ));

        if (
            !$this->api->currentUser()->isCurrentUserAdmin() &&
            !$this->api->currentUser()->isCurrentUserSystem()
        ) {
            if ($this->api->currentUser()->isCurrentUserClient()) {
                $this->add('H3')->setText('Actions:');
                $b = $this->add('Button')->set('Request For Quotation');
                $b->addStyle('margin-bottom','10px');
                $b->js('click', array(
                    $this->js()->univ()->redirect($this->api->url('quotes/rfq'))
                ));

                $b = $this->add('Button')->set('Create Task');
                $b->addStyle('margin-bottom','10px');
                $b->js('click', array(
                    $this->js()->univ()->redirect($this->api->url('tasks/new'))
                ));
            }

            //$this->add('View_Switcher');
            $this->add('View_Dashboard',array(
                'allow_add'=>false,'allow_edit'=>false,'allow_del'=>true,
                'edit_fields'=>$this->api->currentUser()->getDashboardFormFields(),
                'show_fields'=>$this->api->currentUser()->getDashboardGridFields(),
            ));
        }

    }

	function page_time(){
	
		if (!$_GET['task_id']) {
			throw $this->exception('task_id must be provided!');
		}
	
		$this->api->stickyGet('task_id');
		$model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
		$crud=$this->add('CRUD');
        if ($this->api->currentUser()->isCurrentUserClient()){
            $crud->setModel($model,
                array('spent_time','comment','date'),
                array('user','estimate','comment','date','remove_billing')
            );
        }else{
            $crud->setModel($model,
                array('spent_time','comment','date','remove_billing'),
                array('user','spent_time','comment','date','remove_billing')
            );
        }
		if ($crud->grid){
            $crud->grid->addClass('zebra bordered');
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
        $descr_view->add('H4')->set('Description');
        $descr_view->add('View')->setHtml( $this->api->colubris->makeUrls($task->get('descr_original')) );

        // left view
        $left_view = $v->add('View')->setClass('span6 right');
        $left_view->add('H4')->set('Attachments');

       	$model=$left_view->add('Model_Attach')->addCondition('task_id',$_GET['task_id']);
       	$crud=$left_view->add('CRUD',array(
               'grid_class' => 'Grid_Attachments',
           ));
       	$crud->setModel($model,
       			array('description','file_id'),
       			array('description','file','file_thumb','updated_dts')
       	);

           // right view
        $right_view = $v->add('View')->setClass('span6 left');
        $right_view->add('H4')->set('Comments');

       	$cr=$right_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

       	$m=$right_view->add('Model_Taskcomment')
       			->addCondition('task_id',$_GET['task_id']);
       	$cr->setModel($m,
       			array('text','file_id'),
       			array('text','user','file','file_thumb','created_dts')
       	);
       	if($cr->grid){
            $cr->grid->addClass('zebra bordered');
       		$cr->add_button->setLabel('Add Comment');
       	}
       	if($_GET['delete']){
       		$comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
       		$comment->delete();
       		$cr->js()->reload()->execute();
       	}

    }


}
