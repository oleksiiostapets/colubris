<?php

class page_team_quotes_rfq_view extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');
    	
        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        $this->add('P')->set('Estimated - '.$quote->get('estimated'));
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);
        
        $cr = $this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $cr->setModel($requirements,
        		array('estimate'),
        		array('name','descr','estimate','spent_time','file','user')
        		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }
        
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
       	if($cr->grid){
    		$cr->add_button->setLabel('Add Comment');
    	}
    }
    
}