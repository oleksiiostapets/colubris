<?php

class page_manager_project_add extends Page {
    function page_index(){

        $this->add('H1')->set('Add new Project');

        $this->api->stickyGet('return');
        
        $form=$this->add('Form');
        $m=$this->setModel('Model_Project');
        $form->setModel($m);
        
        $form->addSubmit('Save');
        
        if($form->isSubmitted()){
            $form->model->set($form->get());
            $form->model->save();

            $this->api->redirect($this->api->url($_GET['return'],array('project'=>$form->model->get('name'),'project_id'=>$form->model->get('id'))));
        }
    }
    
}
