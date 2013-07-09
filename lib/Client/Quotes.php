<?php
class Client_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        $this->add('H4')->set('1. Quotes requested');
        $this->quotes=$grid=$this->add('Grid');
        $m=$grid->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','quotation_requested');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        $grid->addColumn('button','edit');
        if($_GET['edit']){
            $this->js()->univ()->redirect($this->api->url('/client/rfq/step2',
                        array('quote_id'=>$_GET['edit'])))
                ->execute();
        }
        
        $this->add('H4')->set('2. Quotes not estimated (developer returned)');
        $this->quotes=$cr=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','not_estimated');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        if($cr->grid){
        	$cr->grid->addColumn('button','edit');
        }
        if($_GET['edit']){
            $this->js()->univ()->redirect($this->api->url('/client/rfq/step2',
                        array('quote_id'=>$_GET['edit'])))
                ->execute();
        }
        
        $this->add('H4')->set('3. Quotes estimated (developer returned)');
        $this->quotes=$cr=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name','estimated'));
        $m->addCondition('status','estimated');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/client/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
                
        $this->add('H4')->set('4. Quotes estimate requested (sent to developers for estimation)');
        $this->quotes=$cr=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','estimate_needed');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
            if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/client/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
    }
}
