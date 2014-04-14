<?php
class Model_Reqcomment_Client extends Model_Reqcomment {
	function init(){
		parent::init();
        //$this->debug();
        $participated_in=$this->add('Model_Project')->forClient();
        $projects_ids=array(0);
        foreach($participated_in as $p){
            $projects_ids[]=$p['id'];
        }
        $jr = $this->join('requirement.id','requirement_id','left','_req');

        $jq = $jr->join('quote.id','quote_id','left','_quote');
        $jq->addField('project_id','project_id');

        $this->addCondition('project_id','IN',$projects_ids);
	}
}
