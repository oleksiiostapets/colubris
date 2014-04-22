<?php
class Model_Taskcomment_Developer extends Model_Taskcomment {
	function init(){
		parent::init();
        //$this->debug();
        $participated_in=$this->add('Model_Project')->notDeleted()->forDeveloper();
        $projects_ids=array(0);
        foreach($participated_in as $p){
            $projects_ids[]=$p['id'];
        }
        $jt = $this->join('task.id','task_id','left','_t');

        $jt->addField('project_id','project_id');

        $this->addCondition('project_id','IN',$projects_ids);
	}
}
