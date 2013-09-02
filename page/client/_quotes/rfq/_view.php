<?php

class page_client_quotes_rfq_view extends page_quotesfunctions {
    function page_index(){

        if (!isset($_GET['quote_id'])) {
            throw $this->exception('Provide $_GET[\'quote_id\']');
        }
    	$this->api->stickyGet('quote_id');
    	
        $this->add('View_RFQBread',array('quotes_link'=>'client/quotes'));
    	    	 
    	$this->add('H1')->set('Requirements for Quotation');

        $quote=$this->add('Model_Quote')->load($_GET['quote_id']);
        
        // Checking client's permission to this quote
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if(!$project->loaded()){
        	$this->api->redirect('/denied');
        }

        $RFQQuote = $this->add('View_RFQQuote',array('quote'=>$quote));
                
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $this->add('View_RFQRequirements',array(
            'requirements'=>$requirements,'quote'=>$quote,'total_view'=>$RFQQuote->total_view,
            'allow_add'=>false,'allow_edit'=>false,'allow_del'=>false
        ));
                
    }

}
