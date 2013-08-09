<?php
class Client_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        
        $v=$this->add('View')->setClass('left');
        
        $b=$v->add('Button')->set('Request For Quotation');
        $b->js('click', array(
        		$this->js()->univ()->redirect($this->api->url('client/quotes/rfq'))
        ));
        
        $v=$this->add('View')->setClass('clear');

        $this->add('P');
        
        $cr=$this->add('Grid_Quotes');
        $m=$this->add('Model_Quote');
        //$m->addCondition('client_id',$this->api->auth->model['client_id']);
        $cr->setModel($m,array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status'));
        $cr->addFormatter('status','status');
       	$cr->addColumn('button','edit');
        if($_GET['edit']){
        	$this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/step2',
        			array('quote_id'=>$_GET['edit'])))
        			->execute();
        }
        $cr->addColumn('button','details');
        if($_GET['details']){
        	$this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/view',
        			array('quote_id'=>$_GET['details'])))
        			->execute();
        }
       	$cr->addColumn('button','approve','Approve Estimation');
        if($_GET['approve']){
        	$quote=$this->add('Model_Quote')->load($_GET['approve']);
        	$quote->set('status','estimation_approved');
        	$quote->save();
        	$this->api->redirect($this->api->url('/client/quotes'));
        }
        
        $this->add('P');
    }
}
