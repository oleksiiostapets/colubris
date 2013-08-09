<?php
class View_Report extends View {
    function init(){
        parent::init();

        $cr=$this->add('Grid');
        $m=$this->add('Model_Task_Report'); //->debug();

        $j = $m->join('task_time.task_id','id','left','_tt');
        $j->addField('spent','spent_time');
        $j->addField('date','date');
        $m->addCondition('spent','>','0');

        $jr = $m->join('requirement','requirement_id','left','_req');
        $jr->addField('quote_id','quote_id');

        if($this->api->recall('project_id')>0){
            $m->addCondition('project_id',$this->api->recall('project_id'));
        }
        if($this->api->recall('quote_id')>0){
            $m->addCondition('quote_id',$this->api->recall('quote_id'));
        }
        if($this->api->recall('assigned_id')>0){
            $m->addCondition('assigned_id',$this->api->recall('assigned_id'));
        }
        if($this->api->recall('date_from')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_from'))));
            $m->addCondition('date','>=',$date);
        }
        if($this->api->recall('date_to')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_to'))));
            $m->addCondition('date','<=',$date);
        }

        $cr->setModel($m,array('project','quote','name','status','type','estimate','requester','assigned','spent','date'));
    }
}
