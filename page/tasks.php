<?php
class page_tasks extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeTaskList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Tasks',
                    'url' => 'tasks',
                ),
            )
        ));

        $this->add('View_Switcher');

        $conf_projects_ids=$this->api->getConfig("pivotal/projects");
        foreach($conf_projects_ids as $pivo_project_id => $col_project_id){
            if($this->api->recall('project_id')==$col_project_id){
                $cPivotal=$this->add('ol_pivotal/Controller_pivotal');
                $cPivotal->importStories($pivo_project_id);
            }
        }

        $this->add('View_TasksCRUD',array(
            'newtask_link'=>'tasks/new',
            'allow_add'=>false,'allow_edit'=>false,'allow_del'=>true,
        ));

    }

    function page_time(){

        if (!$_GET['task_id']) {
            throw $this->exception('task_id must be provided!');
        }

        $this->api->stickyGet('task_id');
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
        $descr_view->add('View')->setHtml( $this->api->colubris->makeUrls(nl2br($task->get('descr_original'))) );

        /*
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
//    			array()
        );
*/

        $comments_view = $v->add('View');
        $comments_view->add('H4')->set('Comments');

        $cr=$comments_view->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

        $m=$comments_view->add('Model_Taskcomment')
            ->addCondition('task_id',$_GET['task_id']);

        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts')
//    			array()
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