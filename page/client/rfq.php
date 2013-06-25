<?php

class page_client_rfq extends Page {
    function page_index(){

        $this->add('H1')->set('New Request for Quotation');

        $form=$this->add('Form');

        $m=$this->setModel('Model_Quote');
        $form->setModel($m,array('project_id','name'));
        //$form->getElement('project')->set($_GET['project']);
        //$form->getElement('project_id')->set($_GET['project_id']);
        $add_button=$form->add('Button')->set("New Project")->addClass('add_project');
        $add_button->js('click', $this->js()->univ()->redirect($this->api->url('client/project/add',array('return'=>'client/rfq'))));
        
        $form->add('Order')->move($add_button,'before','name')->now();
        
        $form->addSubmit('Next');
        
        if($form->isSubmitted()){
        	$js=array();
        	$form->model->set('user_id',$this->api->auth->model['id']);
        	$form->update();
        	$this->api->redirect($this->api->url('/client/rfq/step2',array('quote_id'=>$form->model->get('id'))));
        }
        
    }

}
