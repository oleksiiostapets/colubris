<?php
class Model_Project extends Model_Auditable {
	public $table='project';
    public $right;

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


        $this->right = $this->add('Model_User_Right');
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
            if( !isset($this->app->is_test_app)) $m['deleted_id']=$m->app->currentUser()->get('id');
		});
        $this->addHook('afterInsert', function($m,$id){
//            var_dump($m->app->currentUser()->get());
            $model_participant = $m->add('Model_Participant');
            $model_participant->set([
                'user_id'=>$m->app->currentUser()->get('id'),
                'project_id'=>$id
            ])->save();
        });
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


    /**
     *
     * API methods
     *
     */
    function prepareForSelect(Model_User $u){
        if(!$this->right->canManageAllRecords($u['id'])){
            $this->participateIn();
        }

        $fields = ['id'];

        if($this->right->canSeeProjects($u['id'])){
            $fields = array('id','name','descr','client_id','client','demo_url','prod_url','repository','organisation_id','is_deleted','is_deleted','deleted_id','spent_time');
        }else{
            throw $this->exception('This User cannot see projects','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $fields = ['id'];

        if($this->right->canAddProjects($u['id'])){
            $fields = array('id','name','descr','client_id','demo_url','prod_url','repository','organisation_id','is_deleted','is_deleted','deleted_id','spent_time');
        }else{
            throw $this->exception('This User cannot add projects','API_CannotAdd');
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
        if(!$this->right->canManageAllRecords($u['id'])){
            $this->participateIn();
        }

        $fields = ['id'];

        if($this->right->canEditProjects($u['id'])){
            $fields = array('id','name','user_id','general_description','issued','duration','deadline','durdead','html','status','is_deleted','deleted_id','organisation_id','created_dts','updated_dts','expires_dts','is_archived','warranty_end','show_time_to_client','client_id','client_name','client_email','estimated','spent_time');
        }else{
            throw $this->exception('This User cannon edit quotes','API_CannotEdit');
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
        if(!$this->right->canManageAllRecords($u['id'])){
            $this->participateIn();
        }

        if($this->right->canDeleteProjects($u['id'])) return $this;

        throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
    }

}
