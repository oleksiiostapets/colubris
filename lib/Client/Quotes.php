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
        
        $this->add('H4')->set('2. Other Quotes');
        $grid=$this->add('Grid');
        $m=$grid->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','<>','quotation_requested');
        $m->addCondition('client_id',$this->api->auth->model['client_id']);
    }
}
