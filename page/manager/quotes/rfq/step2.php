<?php

class page_manager_quotes_rfq_step2 extends page_quotesfunctions {
    function page_index(){

    	$this->api->stickyGet('quote_id');

        $this->add('View_RFQBread',array('quotes_link'=>'manager/quotes'));
    	    	
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

        $RFQQuote = $this->add('View_RFQQuote',array('quote'=>$quote));

        $this->add('View_RFQRequirements',array(
            'requirements'=>$requirements,'quote'=>$quote,'total_view'=>$RFQQuote->total_view,
            'allow_add'=>false,'allow_edit'=>true,'allow_del'=>true
        ));
        
        $this->add('View_RFQRequirement');
        
    }

    
}
