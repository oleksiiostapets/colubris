<?php
class page_task extends Page {
	private $task;
    function page_index(){

        $this->checkUserRights();
	    $this->addBC();

        $this->task = $this->add('Model_Task')->restrictedUsers();//->debug();

        if ($_GET['task_id']) {
            $this->addEditForm();
        } else {
            $this->addCreateForm();
        }
    }
    protected function addCreateForm() {
        $this->add('Form_Task',['m'=>$this->task]);
    }
    protected function addEditForm() {
        $this->api->stickyGet('task_id');
        $this->task->tryLoad($_GET['task_id']);
        if(!$this->task->loaded()){
            throw $this->exception('Task not exist!','Exception_Task');
        }

        $_GET['project_id'] = $this->task->get('project_id');
	    //$this->task=$this->add('Model_Task_RestrictedUsers');
	    $this->task->forTaskForm();
	    $this->task->load($_GET['task_id']);

	    $this->add('H3')->set('Task details:');
	    $this->add('Form_Task',['m'=>$this->task]);

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
		if ($this->app->model_user_rights->canSeeTime()){
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
					'name' => ($this->task)?$this->task->get('name'):'Create new Task',
					'url' => 'task',
				),
			)
		));
	}
    protected function checkUserRights() {
        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->user_access->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
    }

}