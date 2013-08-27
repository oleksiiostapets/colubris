<?php

class page_team_quotes_rfq_estimate extends page_quotesfunctions {
    function page_index(){

        if (!isset($_GET['quote_id'])) {
            throw $this->exception('Provide $_GET[\'quote_id\']');
        }
    	$this->api->stickyGet('quote_id');
    	
        $this->add('View_RFQBread',array('quotes_link'=>'team/quotes'));

        $this->add('H1')->set('Quotation Estimate');
    	    	 
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
        
        $RFQQuote = $this->add('View_RFQQuote',array('quote'=>$quote));
                
        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);

        $this->add('View_RFQRequirements',array(
            'requirements'=>$requirements,'quote'=>$quote,'total_view'=>$RFQQuote->total_view,
            'allow_add'=>false,'allow_edit'=>true,'allow_del'=>false,'edit_fields'=>array('estimate')
        ));
                
        $finished=$this->add('Button')->set('Estimation finished','estimation_finished');
        $finished->js('click')->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'estimated')));

        $finished=$this->add('Button')->set('Cannot estimate','cannot_estimate');
        $finished->js('click')->univ()->redirect($this->api->url(null,array('quote_id'=>$_GET['quote_id'],'action'=>'not_estimated')));
    }
    
}
