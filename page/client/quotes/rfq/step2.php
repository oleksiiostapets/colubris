<?php

class page_client_quotes_rfq_step2 extends page_quotesfunctions {
    function page_index(){

    	$this->api->stickyGet('quote_id');


        $this->add('View_RFQBread',array('quotes_link'=>'client/quotes'));
    	    	
        $this->add('P');
        
        $v=$this->add('View')->setClass('left');
        $v->add('H1')->set('Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        // Checking client's permission to this quote
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( (!$project->loaded()) || ( ($quote->get('status')!="quotation_requested") && ($quote->get('status')!="not_estimated") ) ){
        	$this->api->redirect('/denied');
        }

        $this->add('View_RFQQuote',array('quote'=>$quote));
                
        $v=$this->add('View')->setClass('right');
        $v->add('P')->set('Requirements, which will be added in the future increase estimation.');
        
        $v=$this->add('View')->setClass('clear');
        
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $this->add('View_RFQRequirements',array('requirements'=>$requirements,'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true));
                
        $this->add('View_RFQRequirement');
                
    }

}
