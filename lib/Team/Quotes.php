<?php
class Team_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);

        $participated_in=$this->add('Model_Participant')->tryLoadBy('user_id',$this->api->auth->model['id']);
        $projects_ids="";
        foreach($participated_in as $p){
        	if($projects_ids=="") $projects_ids=$p['project_id'];
        	else $projects_ids=$projects_ids.','.$p['project_id'];
        }
        
        $this->add('H4')->set('1. Quotes estimate needed');
        $this->quotes=$grid=$this->add('Grid');
        $m=$grid->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','estimate_needed');
        $m->addCondition('project_id','in',$projects_ids);
        $grid->addColumn('button','estimate');
        if($_GET['estimate']){
            $this->js()->univ()->redirect($this->api->url('/team/rfq/estimate',
                        array('quote_id'=>$_GET['estimate'])))
                ->execute();
        }
        
        $this->add('H4')->set('2. Quotes not estimated (developer returned)');
        $this->quotes=$cr=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name'));
        $m->addCondition('status','not_estimated');
        $m->addCondition('project_id','in',$projects_ids);
            if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/team/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
        
        $this->add('H4')->set('3. Quotes estimated (developer returned)');
        $this->quotes=$cr=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $m=$cr->setModel('Quote',array('project','user','name','estimated'));
        $m->addCondition('status','estimated');
        $m->addCondition('project_id','in',$projects_ids);
        if($cr->grid){
        	$cr->grid->addColumn('button','details');
        }
        if($_GET['details']){
            $this->js()->univ()->redirect($this->api->url('/team/rfq/view',
                        array('quote_id'=>$_GET['details'])))
                ->execute();
        }
    }
}
