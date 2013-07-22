<?php

class page_manager_quotes_rfq_step2 extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');


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
                    'name' => 'Request for Quotation (requirements)',
                    'url' => 'manager/quotes/rfq',
                ),
            )
        ));
    	
        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);
        
        $this->add('P');
        
        $v=$this->add('View')->setClass('left');
        $v->add('H1')->set('Requirements for Quotation');
        
        if (  (count($requirements->getRows())>0) && ( ($quote['status']=='quotation_requested') || ($quote['status']=='not_estimated') )  ){
        	$v=$this->add('View')->setClass('right');
        	
	        $estimation=$v->add('Button')->set('Request for estimate','estimation');
	        $estimation->js('click', array(
	            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'estimation')))
	        ));
        
        }
        
        $v=$this->add('View')->setClass('clear');
        
        if($_GET['action']=='estimation'){
        	$quote->set('status','estimate_needed');
        	$quote->save();
        	$this->api->redirect($this->api->url('/manager/quotes'));
        }
        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        $this->add('P')->set('General requirement - '.$quote->get('general'));
        
        $v=$this->add('View')->setClass('left');
        $v->add('H4')->set('Requirements:');
        
        $v=$this->add('View')->setClass('right');
        $v->add('View')->setClass('red_color')->set('Estimated: '.$quote->get('estimated').'hours');
        
        $v=$this->add('View')->setClass('clear');
        
        $cr = $this->add('CRUD',array('allow_add'=>false));
        $cr->setModel($requirements,
        		array('name','descr','estimate','file_id'),
        		array('name','estimate','spent_time','file','user')
        		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','details');
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
        
    }

    function page_details(){
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
    	
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->makeUrls($req->get('descr')));
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
