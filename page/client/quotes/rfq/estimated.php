<?php

class page_client_quotes_rfq_estimated extends Page {
    function page_index(){

    	$this->api->stickyGet('quote_id');
    	
        $this->add('H1')->set('Estimated Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        if($_GET['action']=='approve'){
        	$quote->set('status','estimation_approved');
        	$quote->save();
        	$this->api->redirect($this->api->url('/client/quotes'));
        }
        
        // Checking client's permission to this quote
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( (!$project->loaded()) || ($quote->get('status')!="estimated") ){
        	$this->api->redirect('/denied');
        }

        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$quote->get('project'));
        $this->add('P')->set('User - '.$quote->get('user'));
        $this->add('P')->set('Name - '.$quote->get('name'));
        
        $this->add('H4')->set('Requirements:');
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $cr = $this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $cr->setModel($requirements,
        		array('name','descr','estimate','file_id'),
        		array('name','descr','estimate','spent_time','file','user')
        		);
        if($cr->grid){
        	$cr->grid->addColumn('expander','comments');
        	$cr->grid->addFormatter('file','download');
        }
        
        $approve=$this->add('Button')->set('Approve estimation','approve');
        $approve->js('click', array(
            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'approve')))
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
    }
    
}
