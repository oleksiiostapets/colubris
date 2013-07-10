<?php

class page_manager_quotes_rfq_step2 extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');
    	
        $this->add('H1')->set('Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        if($_GET['action']=='estimation'){
        	$quote->set('status','estimate_needed');
        	$quote->save();
        	$this->api->redirect($this->api->url('/manager/quotes'));
        }
        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);
        
        $cr = $this->add('CRUD',array('allow_add'=>false));
        $cr->setModel($requirements,
        		array('name','descr','estimate','file_id'),
        		array('name','descr','estimate','file','user')
        		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }
        
        $this->add('H4')->set('New Requirement:');
        
        $form=$this->add('Form');
        $m=$this->setModel('Model_Requirement');
        $form->setModel($m,array('name','descr','file_id'));
        $form->addSubmit('Save');

        if($form->isSubmitted()){
        	$form->model->set('user_id',$this->api->auth->model['id']);
        	$form->model->set('quote_id',$_GET['quote_id']);
        	$form->update();
        	$this->api->redirect(null);
        }
        
        $estimation=$form->addButton('Request for estimate','estimation');
        $estimation->js('click', array(
            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'estimation')))
        ));
        
    }

    function page_comments(){
    	$this->api->stickyGET('requirement_id');
    	$cr=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false));
    	 
    	$m=$this->add('Model_Reqcomment')
    	->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text'),
    			array('text','user')
    	);
    	$cr->add_button->setLabel('Add Comment');
    }
    
}
