<?php
class View_Report extends View {
    function init(){
        parent::init();

        $cr=$this->add('Grid');
        $m=$this->add('Model_Task_Report');//->debug();

        $j = $m->join('task_time.task_id','id','left','_tt');
        $j->addField('spent','spent_time');
        $j->addField('remove_billing','remove_billing');
        $j->addField('date','date');
        $j->addField('performer_id','user_id');
        $m->addCondition('spent','>','0');
        $m->addCondition('remove_billing',false);

        $ju = $j->join('user.id','user_id','left','_tu');
        $ju->addField('performer','name');

        $jr = $m->join('requirement','requirement_id','left','_req');
        $jr->addField('quote_id','quote_id');

        $jp = $m->join('project','project_id','left','_pr');
        $jp->addField('organisation_id','organisation_id');
        $m->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        if($this->api->recall('project_id')>0){
            $m->addCondition('project_id',$this->api->recall('project_id'));
        }
        if($this->api->recall('quote_id')>0){
            $m->addCondition('quote_id',$this->api->recall('quote_id'));
        }
        if($this->api->recall('quote_id')==-1){
            $m->addCondition('quote_id','>',0);
        }
        if($this->api->recall('quote_id')==-2){
            $m->addCondition('quote_id',null);
        }
        if($this->api->recall('performer_id')>0){
            $m->addCondition('performer_id',$this->api->recall('performer_id'));
        }
        if($this->api->recall('date_from')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_from'))));
            $m->addCondition('date','>=',$date);
        }
        if($this->api->recall('date_to')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_to'))));
            $m->addCondition('date','<=',$date);
        }

        $cr->setModel($m,$this->grid_show_fields);

        $cr->addTotals(array('estimate','spent'));
    }
}
