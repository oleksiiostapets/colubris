<?
class Model_Project extends Model_Project_Base {
    function init(){
        parent::init();
        $this->addCondition('is_deleted',false);
    }
    function forClient() {
        $this->addCondition('client_id',$this->api->auth->model['client_id']);
    }

    // TODO refactor this. Maybe join?
    function participateIn() {
        $participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->api->auth->model['id']);
        $projects_ids="";
        foreach($participated_in->getRows() as $p){
            if($projects_ids=="") $projects_ids=$p['project_id'];
            else $projects_ids=$projects_ids.','.$p['project_id'];
        }
        $this->addCondition('id','in',$projects_ids);
    }
    function forDeveloper() {
        $this->participateIn();
    }
}
