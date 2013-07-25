<?php

class page_client_quotes_rfq_view extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');
    	
        $this->add('H1')->set('Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        // Checking client's permission to this quote
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if(!$project->loaded()){
        	$this->api->redirect('/denied');
        }

        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        $this->add('P')->set('General requirement - '.$quote->get('general'));
        $this->add('P')->set('Estimated - '.$quote->get('estimated'));
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $cr = $this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $cr->setModel($requirements,
        		array('name','descr','file_id'),
        		array('name','estimate','spent_time','file','user')
        		);
        if($cr->grid){
        	$cr->grid->addColumn('expander','details');
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }        
        
    }

    function page_details(){
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
    	
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->makeUrls($req->get('descr')));
    }
    function page_comments(){
    	$this->api->stickyGET('requirement_id');
    	$cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));
    	 
    	$m=$this->add('Model_Reqcomment')
    			->addCondition('requirement_id',$_GET['requirement_id']);
    	$cr->setModel($m,
    			array('text'),
    			array('text','user')
    	);
    	if($cr->grid){
    		$cr->add_button->setLabel('Add Comment');
    		$cr->grid->setFormatter('text','text');
    	}
    	if($_GET['delete']){
    		$comment=$this->add('Model_Reqcomment')->load($_GET['delete']);
    		$comment->delete();
    		$cr->js()->reload()->execute();
    	}
    }
    
}
