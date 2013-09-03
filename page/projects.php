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
        if( !$this->api->currentUser()->canSeeProjectList() ){
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

        $m = $this->add('Model_Project');
        if ($this->api->currentUser()->isClient())    $m->forClient();
        if ($this->api->currentUser()->isDeveloper()) $m->forDeveloper();

        $cr=$this->add('CRUD', array(
            'allow_del'  =>  $this->api->currentUser()->canDeleteProject(),
            'allow_edit' =>  $this->api->currentUser()->canEditProject(),
            'allow_add'  =>  $this->api->currentUser()->canCreateProject()
        ));

        $cr->setModel($m,
            $this->api->currentUser()->getProjectFormFields(),
            $this->api->currentUser()->getProjectGridFields()
        );
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->grid->addPaginator(25);
			if ($this->api->currentUser()->canSeeProjectParticipantes()) {
                $cr->grid->addColumn('expander','participants');
            }
			if ($this->api->currentUser()->canSeeProjectTasks()) {
                $cr->grid->addColumn('expander','tasks');
            }
        }

    }
    function page_participants(){
        if (!$this->api->currentUser()->canSeeProjectParticipantes()) {
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
        $this->api->stickyGET('project_id');
        $m = $this->add('Model_Participant')
                ->addCondition('project_id',$_GET['project_id']);
        $this->add('CRUD')->setModel($m);
    }
    function page_tasks(){
        if (!$this->api->currentUser()->canSeeProjectTasks()) {
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
        }
    }
//    function page_add(){
//        $this->api->stickyGet('return');
//
//        $this->add('H1')->set('Add new Project');
//
//        $form=$this->add('Form');
//        $m=$this->setModel('Model_Project');
//        $form->setModel($m);
//
//        $form->addSubmit('Save');
//
//        if($form->isSubmitted()){
//            $form->model->set($form->get());
//            $form->model->save();
//
//            $this->api->redirect($this->api->url($_GET['return'],array('project'=>$form->model->get('name'),'project_id'=>$form->model->get('id'))));
//        }
//    }
}