<?php
class Team_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        $v=$this->add('View')->setClass('span6 left');
        
        $v->add('H4')->set('Quotes estimate needed');
        $this->quotes=$grid=$v->add('Grid');
        $m=$grid->setModel('Quote_Participant',array('project','user','name'));
        $m->addCondition('status','estimate_needed');
        $grid->addColumn('button','estimate');
        if($_GET['estimate']){
            $this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/estimate',
                        array('quote_id'=>$_GET['estimate'])))
                ->execute();
        }
        
        $v->add('H4')->set('Quotes with approved estimation');
        $this->quotes=$grid=$v->add('Grid');
        $m=$grid->setModel('Quote_Participant',array('project','user','name','estimated','spent_time'));
        $m->addCondition('status','estimation_approved');
        $grid->addColumn('button','details');
        if($_GET['details']){
        	$this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/view',
        			array('quote_id'=>$_GET['details'])))
        			->execute();
        }
        
        $v->add('H4')->set('Quotes not estimated (developer returned)');
        $this->quotes=$cr=$v->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote_Participant',array('project','user','name'));
        $m->addCondition('status','not_estimated');
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
        
        
        $v=$this->add('View')->setClass('span6 right');
        
        $v->add('H4')->set('Quotes estimated (developer returned)');
        $this->quotes=$cr=$v->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote_Participant',array('project','user','name','estimated'));
        $m->addCondition('status','estimated');
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/team/quotes/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
    }
}
