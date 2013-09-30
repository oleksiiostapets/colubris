<?php
class View_Report extends View {
    function init(){
        parent::init();

        $m=$this->add('Model_TaskTime');//->debug();
        $m->getField('user_id')->caption('Performer');
        $m->getField('spent_time')->caption('Spent');
        $m->addCondition('spent_time','>','0');
        $m->addCondition('remove_billing',false);

        $j_task = $m->join('task.id','task_id','left','_t');
        $j_task->addField('task_name','name');
        $j_task->addField('status','status');
        $j_task->addField('type','type');
        $j_task->addField('estimate','estimate');
        $j_task->addField('project_id','project_id');

        $j_project = $j_task->join('project.id','project_id','left','_p');
        $j_project->addField('project_name','name');

        $j_req = $j_task->join('requirement','requirement_id','left','_req');
        $j_req->addField('quote_id','quote_id');

        if( ($this->api->currentUser()->isDeveloper()) || $this->api->currentUser()->isClient() ){
            $mp=$this->add('Model_Project');
            if($this->api->currentUser()->isDeveloper()) $projects=$mp->forDeveloper();
            if($this->api->currentUser()->isClient()) $projects=$mp->forClient();
            $projects_ids="";
            foreach($projects->getRows() as $p){
                if($projects_ids=="") $projects_ids=$p['id'];
                else $projects_ids=$projects_ids.','.$p['id'];
            }
            $m->addCondition('project_id','in',$projects_ids);
        }

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
            $m->addCondition('user_id',$this->api->recall('performer_id'));
        }
        if($this->api->recall('date_from')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_from'))));
            $m->addCondition('date','>=',$date);
        }
        if($this->api->recall('date_to')!=''){
            $date=date('Y-m-d',strtotime(str_replace('/','-',$this->api->recall('date_to'))));
            $m->addCondition('date','<=',$date);
        }

        $v=$this->add('View');
        $v->setClass('right');
        $export_button=$v->add('Button')->set('Export to CSV');
        $export_button->js('click',
            $this->js()->univ()->redirect($this->api->url(null,array('action'=>'export')))
        );

        if ($_GET['action']=='export'){
            header('Content-Disposition: attachment; filename="report.csv"');
            $header=implode(",",$this->export_fields).",";

            $total_spent=0;
            $data="";
            foreach($m->getRows() as $row){
                $total_spent+=$row['spent_time'];
                foreach($this->export_fields as $field_name){
                    $data.=substr($row[$field_name],0,30).",";
                }
                $data.="\n";
                //$data.=substr($row["project_name"],0,30).",".substr($row["task_name"],0,30).",".$row["status"].",".$row["type"].",".$row["estimate"].",".$row["spent_time"].",".$row["date"].",".$row["user"]."\n";
            }
            $data.="TOTAL, , , , ,$total_spent, , ,";
            print "$header\n$data";
            exit;
        }

        $v=$this->add('View');
        $v->setClass('cc');

        $cr=$this->add('Grid');
        $cr->addClass('zebra bordered');

        $cr->setModel($m,$this->grid_show_fields);
        $cr->addFormatter('task_name','wrap');

        $cr->addTotals(array('spent_time'));

        $cr->addColumn('expander','more');
    }
}
