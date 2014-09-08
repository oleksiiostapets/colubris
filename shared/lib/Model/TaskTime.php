<?php
class Model_TaskTime extends Model_Table {
    public $table='task_time';

    function init(){
        parent::init();

        $this->hasOne('Task','task_id');
        $this->getField('task_id')->mandatory(true);

        $this->hasOne('User','user_id');
        $this->getField('user_id')->mandatory(true);

        $this->addField('spent_time')->mandatory(true);

        $this->addField('comment')->dataType('text');

        $this->addField('date')->dataType('date');

        $this->addField('remove_billing')->type('boolean')->defaultValue('0')->caption('Remove from billing');

        $this->addHook('beforeInsert', function($m,$q){
        	if($m['date']=='') $q->set('date', $q->expr('now()'));
        	$q->set('user_id', $q->api->auth->model['id']);
        });
        
       	$this->setOrder('date',true);

	}
	public function forReportsGrid(){
		$this->getField('user_id')->caption('Performer');
		$this->getField('spent_time')->caption('Spent');

		$this->addCondition('spent_time','>','0');



		$j_task = $this->join('task.id','task_id','left','_t');
		$j_task->addField('task_name','name');
		$j_task->addField('status','status');
		$j_task->addField('type','type');
//        $j_task->addField('estimate','estimate');
		$j_task->addField('project_id','project_id');
		$j_task->addField('organisation_id','organisation_id');

		$j_project = $j_task->join('project.id','project_id','left','_p');
		$f1=$j_project->addField('project_name','name');
		$f1->sortable(true);

		$j_req = $j_task->join('requirement','requirement_id','left','_req');
		$j_req->addField('quote_id','quote_id');

		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));

        $projects=$this->add('Model_Project')->notDeleted();
        $projects_ids="";
        foreach($projects->getRows() as $p){
            if($projects_ids=="") $projects_ids=$p['id'];
            else $projects_ids=$projects_ids.','.$p['id'];
        }
        $this->addCondition('project_id','in',$projects_ids);

		$this->addGetConditions();
	}
	function addGetConditions() {
		if($_GET['including']==1){
			$this->addCondition('remove_billing',false);
		}elseif($_GET['including']==2){
			$this->addCondition('remove_billing',true);
		}

		if ($_GET['project']>0) {
			$this->addCondition('project_id',$_GET['project']);
		}

		if ($_GET['quote']>0) {
			$this->addCondition('quote_id',$_GET['quote']);
		}

		if($_GET['performer_id']>0){
			$this->addCondition('user_id',$_GET['performer_id']);
		}

		if($_GET['date_from']!=''){
			$date=date('Y-m-d',strtotime(str_replace('/','-',$_GET['date_from'])));
			$this->addCondition('date','>=',$date);
		}
		if($_GET['date_to']!=''){
			$date=date('Y-m-d',strtotime(str_replace('/','-',$_GET['date_to'])));
			$this->addCondition('date','<=',$date);
		}

		return $this;
	}
}
