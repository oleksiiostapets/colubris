<?php
class page_task extends Page_Functional {
	private $task;
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->user_access->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->api->stickyGet('task_id');

        $mp=$this->add('Model_Project')->notDeleted();
        if($this->api->currentUser()->isDeveloper()){
            $mp->forDeveloper();
        }elseif($this->api->currentUser()->isClient()){
            $mp->forClient();
        }

        $this->task=$this->add('Model_Task');//->debug();
        $this->task->tryLoad($_GET['task_id']);
        if(!$this->task->loaded()){
            throw $this->exception('Task not exist!','Exception_Task');
        }

	    $permission_granted=false;
        foreach($mp->getRows() as $pr){
            if ($pr['id']==$this->task->get('project_id')) $permission_granted=true;
        }
        if(!$permission_granted) throw $this->exception('You cannot see this page','Exception_Denied');

	    $this->addBC();

        $_GET['project_id']=$this->task->get('project_id');
	    $this->task=$this->add('Model_Task_RestrictedUsers');
	    $this->task->forTaskForm();
	    $this->task->load($_GET['task_id']);

	    $this->add('H3')->set('Task details:');
	    $this->add('Form_Task',['model'=>$this->task]);

	    $this->addTaskTime();

	    $this->addComments();
    }
	protected function addComments(){
		$v = $this->add('View');
		$comments_view = $v->add('View');
		$comments_view->add('H4')->set('Comments');
		$comments_view->add('CRUD_TaskComments',array('task_id'=>$_GET['task_id']));
	}
	protected function addTaskTime(){
		if (!$this->api->currentUser()->isClient()){
			$this->add('H3')->set('Time details:');
			$this->add('CRUD_TaskTime',array('task_id'=>$_GET['task_id']));
		}
	}
	protected function addBC(){
		$this->add('x_bread_crumb/View_BC',array(
			'routes' => array(
				0 => array(
					'name' => 'Home',
				),
				1 => array(
					'name' => 'Tasks',
					'url' => $this->api->url('tasks',array(
							'project_id'=>$this->api->recall('project_id'),
							'quote_id'=>$this->api->recall('quote_id'),
							'requirement_id'=>$this->api->recall('requirement_id'),
							'status'=>$this->api->recall('status'),
							'assigned_id'=>$this->api->recall('assigned_id'),
						)),
				),
				2 => array(
					'name' => $this->task->get('name'),
					'url' => 'task',
				),
			)
		));
	}

}