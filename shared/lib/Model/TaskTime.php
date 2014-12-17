<?php
class Model_TaskTime extends Model_Auditable {
    public $table='task_time';

    function init(){
        parent::init();

        $this->hasOne('Task','task_id');
        $this->getField('task_id')->mandatory(true);

        $this->hasOne('User','user_id');
        $this->getField('user_id')->mandatory(true);

        $this->addField('spent_time')->mandatory(true);

        $this->addField('comment')->type('text');

        $this->addField('date')->type('date');

        $this->addField('remove_billing')->type('boolean')->defaultValue('0')->caption('Remove from billing');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');

        $this->addHook('beforeInsert', function($m,$q){
        	if($m['date']=='') $q->set('date', $q->expr('now()'));
        	$q->set('user_id', $q->app->currentUser()->get('id'));
        });

        $task_join = $this->leftJoin('task.id','task_id','left','_task');
        $task_join->addField('requirement_id','requirement_id');

        $req_join = $task_join->leftJoin('requirement.id','requirement_id','left','_req');
        $req_join->addField('requirement_name','name');

        $quote_join = $req_join->leftJoin('quote.id','quote_id','left','_quote');
        $quote_join->addField('quote_name','name');

       	$this->setOrder('date',true);

	}
	public function forReportsGrid(){
		$this->getField('user_id')->caption('Performer');
		$this->getField('spent_time')->caption('Spent');

		$this->addCondition('spent_time','>','0');



		$j_task = $this->leftJoin('task.id','task_id','left','_t');
		$j_task->addField('task_name','name');
		$j_task->addField('status','status');
		$j_task->addField('type','type');
//        $j_task->addField('estimate','estimate');
		$j_task->addField('project_id','project_id');
		$j_task->addField('organisation_id','organisation_id');

		$j_project = $j_task->leftJoin('project.id','project_id','left','_p');
		$f1=$j_project->addField('project_name','name');
		$f1->sortable(true);

		$j_req = $j_task->leftJoin('requirement','requirement_id','left','_req');
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

        if ($_GET['requirement']>0) {
            $this->addCondition('requirement_id',$_GET['requirement']);
        }

        if($_GET['assigned']>0){
			$this->addCondition('user_id',$_GET['assigned']);
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


    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeTime($u['id'])){
            $fields = array('id','task_id','task','user_id','user','spent_time','comment','date','remove_billing');
        }else{
            throw $this->exception('This User cannot see Times','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canTrackTime($u['id'])){
            $fields = array('id','task_id','user_id','spent_time','comment','date','remove_billing');
        }else{
            throw $this->exception('This User cannon add record','API_CannotAdd');
        }

        foreach ($this->getActualFields() as $f){
            $fo = $this->hasElement($f);
            if(in_array($f, $fields)){
                if($fo) $fo->editable = true;
            }else{
                if($fo) $fo->editable = false;
            }
        }
        return $this;
    }
    function prepareForUpdate(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canTrackTime($u['id'])){
            $fields = array('id','task_id','user_id','spent_time','comment','date','remove_billing','is_deleted');
        }else{
            throw $this->exception('This User cannon edit record','API_CannotEdit');
        }

        foreach ($this->getActualFields() as $f){
            $fo = $this->hasElement($f);
            if(in_array($f, $fields)){
                if($fo) $fo->editable = true;
            }else{
                if($fo) $fo->editable = false;
            }
        }
        return $this;
    }
    function prepareForDelete(Model_User $u){
        $r = $this->add('Model_User_Right');

        if($r->canTrackTime($u['id'])) return $this;

        throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
    }
}
