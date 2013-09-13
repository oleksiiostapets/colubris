<?php
class page_task extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->api->stickyGet('task_id');

        $mp=$this->add('Model_Project');
        if($this->api->currentUser()->isCurrentUserDev()){
            $mp->forDeveloper();
        }elseif($this->api->currentUser()->isCurrentUserClient()){
            $mp->forClient();
        }
        $this->task=$this->add('Model_Task')->tryLoad($_GET['task_id']);
        if(!$this->task->loaded()){
            throw $this->exception('Task not exist!','Exception_Task');
        }
        $permission_granted=false;
        foreach($mp->getRows() as $pr){
            if ($pr['id']==$this->task->get('project_id')) $permission_granted=true;
        }
        if(!$permission_granted) throw $this->exception('You cannot see this page','Exception_Denied');

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'tasks',
                ),
                2 => array(
                    'name' => 'Task',
                    'url' => 'task',
                ),
            )
        ));

        $this->add('View_SwitcherEditTask',array('task'=>$this->task));

        $this->add('H3')->set('Task details:');

        $f=$this->add('Form');
        $f->setModel($this->task,array('name','descr_original','priority','type','status','estimate','requester_id','assigned_id'));
        $f->addSubmit('Save');
        if($f->isSubmitted()){
            $f->update();
            $f->js()->univ()->successMessage('Successfully updated task details')->execute();
        }

        $this->add('H3')->set('Time details:');

        $model=$this->add('Model_TaskTime')->addCondition('task_id',$_GET['task_id']);
        $crud=$this->add('CRUD');
        if ($this->api->auth->model['is_client']){
            $crud->setModel($model,
                array('spent_time','comment','date'),
                array('user','spent_time','comment','date','remove_billing')
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


        $this->add('P');
        $v = $this->add('View');

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
            $cr->grid->setFormatter('text','text');
            $cr->grid->addFormatter('text','wrap');
        }
        if($_GET['delete']){
            $comment=$this->add('Model_Taskcomment')->load($_GET['delete']);
            $comment->delete();
            $cr->js()->reload()->execute();
        }
    }

}