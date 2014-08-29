<?php
class Model_Project extends Model_Auditable {
	public $table='project';

	function init(){
		parent::init();

		$this->addField('name')->mandatory('required');
		$this->addField('descr')->dataType('text');

//        $this->addField('client_id')->refModel('Model_Client');
		$this->hasOne('Client','client_id');

		$this->addField('demo_url');
		$this->addField('prod_url');
		$this->addField('repository');

//        $this->addField('organisation_id')->refModel('Model_Organisation');
		$this->hasOne('Organisation','organisation_id');

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');

		$this->setOrder('name');

//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

        $this->addExpressions();
		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');
		});
	}

	function deleted() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
	function getThisOrganisation() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		return $this;
	}


    function forRole($role) {
        switch ($role) {
            case 'system':
                break;
            case 'admin':
                break;
            case 'admin':
                break;
            case 'manager':
                return $this->forManager();
                break;
            case 'sales':
                break;
            case 'developer':
                return $this->forDeveloper();
                break;
            case 'client':
                return $this->forClient();
                break;
            default:
                throw $this->exception('Wrong role');
        }
    }
    function forClient() {
        $this->addCondition('client_id',$this->app->currentUser()->get('client_id'));
        return $this;
    }
    // TODO refactor this. Maybe join?
    function participateIn() {
        $participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->app->currentUser()->get('id'));
        $projects_ids="";
        foreach($participated_in->getRows() as $p){
            if($projects_ids=="") $projects_ids=$p['project_id'];
            else $projects_ids=$projects_ids.','.$p['project_id'];
        }
        $this->addCondition('id','in',$projects_ids);
        return $this;
    }
    function forDeveloper() {
        return $this->participateIn();
    }
    function forManager(){
        return $this;
    }
    function getAllParticipants($project_id){
        if ($project_id>0){
            $this->tryLoad($project_id);
        }

        // Get all managers from our organisation
        $managers=$this->add('Model_User')->getActive();
        $managers->addCondition('is_manager',true);
        $par_ids=array();
        foreach($managers->getRows() as $u){
                if(!in_array($u['id'],$par_ids)) $par_ids[]=$u['id'];
        }

        if ($project_id>0){
            // Get all developers by project
            $mp=$this->add('Model_Participant');
            $mp->addCondition('project_id',$project_id);
            foreach($mp->getRows() as $u){
                if(!in_array($u['user_id'],$par_ids)) $par_ids[]=$u['user_id'];
            }

            // Get all clients by project
            $mu=$this->add('Model_User')->getActive();
            $mu->addCondition('client_id',$this->get('client_id'));
            foreach($mu->getRows() as $u){
                if(!in_array($u['id'],$par_ids)) $par_ids[]=$u['id'];
            }
        }

        return $par_ids;
    }

    // ------------------------------------------------------------------------------
    //
    //                          Expressions
    //
    // ------------------------------------------------------------------------------

    function addExpressions(){
        $this->addExpression('spent_time')->set(function($m,$q){
            $m_t = $this->add('Model_Task')
                ->addCondition('project_id',$m->getField('id'))
                ->fieldQuery('id');
            $m_tt = $this->add('Model_TaskTime')
                ->addCondition('task_id','in',$m_t);
            return $m_tt->sum('spent_time');
        });
    }
    // Expressions --------------------------------------------------------------
}
