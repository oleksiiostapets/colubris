<?php
class _Team_Quotes extends View {
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
        
        $cr=$this->add('Grid_Quotes',array(
            'role'=>'team',
            'allowed_actions'=>array(
                'details',
                'estimate',
            )
        ));
        $m=$this->add('Model_Quote_Participant');
        $cr->setModel($m,array('project','user','name','estimated','spent_time','durdead','status'));
        $cr->addFormatter('status','status');
        $cr->addPaginator(10);

        $this->add('P');
    }
}
