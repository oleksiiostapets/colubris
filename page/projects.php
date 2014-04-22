<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 9/1/13
 * Time: 8:55 PM
 * To change this template use File | Settings | File Templates.
 */
class page_projects extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->user_access->canSeeProjectList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Projects',
                    'url' => 'projects',
                ),
            )
        ));

        $this->add('H2')->set('Projects');

        $m = $this->add('Model_Project')->notDeleted();
        if ($this->api->currentUser()->isClient())    $m->forClient();
        if ($this->api->currentUser()->isDeveloper()) $m->forDeveloper();

        $cr=$this->add('CRUD', array(
            'allow_del'  =>  $this->app->user_access->canDeleteProject(),
            'allow_edit' =>  $this->app->user_access->canEditProject(),
            'allow_add'  =>  $this->app->user_access->canCreateProject()
        ));

        $cr->setModel($m,
			$this->app->user_access->getProjectFormFields(),
			$this->app->user_access->getProjectGridFields()
        );
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->grid->addPaginator(25);
			if ($this->app->user_access->canSeeProjectParticipantes()) {
                $cr->grid->addColumn('expander','participants');
            }
			if ($this->app->user_access->canSeeProjectTasks()) {
                $cr->grid->addColumn('expander','tasks');
            }
            if ($cr->grid->hasColumn('demo_url')) {
                $cr->grid->addFormatter('demo_url','blankurl');
            }
            if ($cr->grid->hasColumn('prod_url')) {
                $cr->grid->addFormatter('prod_url','blankurl');
            }
            if ($cr->grid->hasColumn('repository')) {
                $cr->grid->addFormatter('repository','blankurl');
            }
        }

    }
    function page_participants(){
        if (!$this->app->user_access->canSeeProjectParticipantes()) {
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
        $this->api->stickyGET('project_id');
        $m = $this->add('Model_Participant')
                ->addCondition('project_id',$_GET['project_id']);
        $this->add('CRUD')->setModel($m);
    }
    function page_tasks(){
        if (!$this->app->user_access->canSeeProjectTasks()) {
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
        $this->api->stickyGET('project_id');
        $cr=$this->add('CRUD',array('grid_class'=>'Grid_Tasks'));
        $m=$this->add('Model_Task')
            ->addCondition('project_id',$_GET['project_id']);
        $cr->setModel($m,
            array('name','descr_original','estimate','priority','type','status','requester_id','assigned_id'),
            array('name','estimate','priority','type','status','spent_time','requester','assigned')
        );
        if($cr->grid){
            $cr->grid->addFormatter('status','status');
            $cr->grid->addFormatter('name','wrap');
            $cr->grid->addPaginator(5);
        }
    }
    function page_add(){
        $this->api->stickyGet('return');

        $this->add('H1')->set('Add new Project');

        $form=$this->add('Form');
        $m=$this->setModel('Model_Project')->notDeleted();
        $form->setModel($m,
            array('name','descr','client','demo_url','prod_url','repository')
        );

        $form->addSubmit('Save');

        if($form->isSubmitted()){
            $form->model->set($form->get());
            $form->model->save();

            $this->api->redirect($this->api->url($_GET['return'],array('project'=>$form->model->get('name'),'project_id'=>$form->model->get('id'))));
        }
    }
}

/*
 *   TODO
 *
 *    SETUP AUTOLOAD   !!!!
 *    THIS IS JUST FOR TEMP FIX !!!
 *
 *
 */



class Grid extends Grid_Advanced {
    function format_blankurl($field){
        if ($this->current_row[$field] != '') {
            $f = $this->current_row[$field];
            if (!preg_match('/^(http:\/\/|https:\/\/)/',$f)) {
                $f = 'http://'.$f;
            }
            $this->current_row_html[$field] = '<a target="_blank" href="'.$f.'">link</a>';
        } else {
            $this->current_row_html[$field] = '';
        }
    }
}