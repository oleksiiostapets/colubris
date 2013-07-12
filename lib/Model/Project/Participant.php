<?
class Model_Project_Participant extends Model_Project {

    function init(){
        parent::init();

        if ($this->api->auth->model['is_developer']){
        	$participated_in=$this->add('Model_Participant')->tryLoadBy('user_id',$this->api->auth->model['id']);
	        $projects_ids="";
	        foreach($participated_in as $p){
	        	if($projects_ids=="") $projects_ids=$p['project_id'];
	        	else $projects_ids=$projects_ids.','.$p['project_id'];
	        }
	        $this->addCondition('id','in',$projects_ids);
        }
    }
    
}
