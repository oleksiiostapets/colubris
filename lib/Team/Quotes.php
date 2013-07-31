<?php
class Team_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        /*
        $v=$this->add('View')->setClass('left');
        
        $b=$v->add('Button')->set('Request For Quotation');
        $b->js('click', array(
        		$this->js()->univ()->redirect($this->api->url('team/quotes/rfq'))
        ));
        
        $v=$this->add('View')->setClass('clear');
        */
        $this->add('P');
        
        $cr=$this->add('Grid_Quotes');
        $m=$this->add('Model_Quote_Participant');
        $cr->setModel($m,array('project','user','name','estimated','spent_time','durdead','status'));
        $cr->addFormatter('status','status');
        
        $cr->addColumn('button','details');
        if($_GET['details']){
        	$this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/view',
        			array('quote_id'=>$_GET['details'])))
        			->execute();
        }
        
        $cr->addColumn('button','estimate');
        if($_GET['estimate']){
        	$this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/estimate',
        			array('quote_id'=>$_GET['estimate'])))
        			->execute();
        }
        
        $this->add('P');
    }
}
