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
        
        $v=$this->add('View')->setClass('span6 left');
        
        $v->add('H4')->set('Quotes requested');
        $this->quotes=$grid=$v->add('Grid');
        $m=$grid->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','quotation_requested');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        $grid->addColumn('button','edit');
        if($_GET['edit']){
            $this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/step2',
                        array('quote_id'=>$_GET['edit'])))
                ->execute();
        }
        
        $v->add('H4')->set('Quotes with approved estimation');
        $this->quotes=$grid=$v->add('Grid');
        $m=$grid->setModel('Quote',array('project','user','name','estimated','spent_time'));
        $m->addCondition('status','estimation_approved');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        $grid->addColumn('button','details');
        if($_GET['details']){
        	$this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/view',
        			array('quote_id'=>$_GET['details'])))
        			->execute();
        }
        
        $v->add('H4')->set('Quotes not estimated (developer returned)');
        $this->quotes=$cr=$v->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','not_estimated');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        if($cr->grid){
        	$cr->grid->addColumn('button','edit');
        }
        if($_GET['edit']){
            $this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/step2',
                        array('quote_id'=>$_GET['edit'])))
                ->execute();
        }
        
        
        $v=$this->add('View')->setClass('span6 right');
        
        $v->add('H4')->set('Quotes estimate requested (sent to developers for estimation)');
        $this->quotes=$cr=$v->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','estimate_needed');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }

        $v->add('H4')->set('Quotes estimated (developer returned)');
        $this->quotes=$cr=$v->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name','estimated'));
        $m->addCondition('status','estimated');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        	$cr->grid->addColumn('button','approve','Approve Estimation');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/client/quotes/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
        if($_GET['approve']){
        	$quote=$this->add('Model_Quote')->load($_GET['approve']);
        	$quote->set('status','estimation_approved');
        	$quote->save();
        	$this->api->redirect($this->api->url('/client/quotes'));
        }
        
    }
}
