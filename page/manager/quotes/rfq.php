<?php

class page_manager_quotes_rfq extends Page {
    function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'manager/quotes',
                ),
                2 => array(
                    'name' => 'Request for Quotation (create)',
                    'url' => 'manager/quotes/rfq',
                ),
            )
        ));

        $this->add('H1')->set('New Request for Quotation');

        $form=$this->add('Form');
/*
        $client=$form->addField('autocomplete/basic','client_id');
        $client->setModel('Client');
        $client->add('Button_NewEntry',null,'after_field')
            ->setLabel('New Client')->setModel('Client',array('name'));
*/
        /*
        $project=$form->addField('dropdown','project_id');
        $project->setModel('Project');
*/
        $m=$this->setModel('Model_Quote');
        $form->setModel($m,array('project_id','name'));
        $form->getElement('project')->set($_GET['project']);
        $form->getElement('project_id')->set($_GET['project_id']);
        $form->getElement('project')->caption='Project\'s name';
        $form->getElement('name')->caption='Quotation\'s name';
        $add_button=$form->add('Button')->set("New Project")->addClass('add_project');
        $add_button->js('click', $this->js()->univ()->redirect($this->api->url('manager/project/add',array('return'=>'manager/quotes/rfq'))));
        
        $form->add('Order')->move($add_button,'before','name')->now();
        
        $form->addSubmit('To the Next Step');
        
        if($form->isSubmitted()){
        	$js=array();
        	$form->model->set('user_id',$this->api->auth->model['id']);
        	$form->model->set('status','quotation_requested');
        	$form->update();
        	$this->api->redirect($this->api->url('/manager/quotes/rfq/step2',array('quote_id'=>$form->model->get('id'))));
        }
        
    }

}
