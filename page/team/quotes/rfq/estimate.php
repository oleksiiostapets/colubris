<?php

class page_team_quotes_rfq_estimate extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');
    	
        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        if($_GET['action']=='estimated'){
        	$quote->set('status','estimated');
        	$quote->save();
        	$this->api->redirect($this->api->url('/team/quotes'));
        }
        
        if($_GET['action']=='not_estimated'){
        	$quote->set('status','not_estimated');
        	$quote->save();
        	$this->api->redirect($this->api->url('/team/quotes'));
        }
        
        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        $this->add('P')->set('General requirement - '.$quote->get('general'));
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);
        
        $cr = $this->add('CRUD',array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>false));
        $cr->setModel($requirements,
        		array('estimate'),
        		array('name','estimate','spent_time','file','user')
        		);
        
        if($cr->grid){
        	$cr->grid->addColumn('expander','details');
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }
        
        $finished=$this->add('Button')->set('Estimation finished','estimation_finished');
        $finished->js('click')->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'estimated')));

        $finished=$this->add('Button')->set('Cannot estimate','cannot_estimate');
        $finished->js('click')->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'not_estimated')));
    }
    
    function page_details(){
    	$this->api->stickyGET('requirement_id');
    	$req=$this->add('Model_Requirement')->load($_GET['requirement_id']);
    	
    	$this->add('View')->setHtml('<strong>Description:</strong> '.$req->get('descr'));
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
