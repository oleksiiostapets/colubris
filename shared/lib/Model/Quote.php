<?php
class Model_Quote extends Model_Auditable {
	public $table="quote";
	function init(){
		parent::init(); //$this->debug();
		if ($this->api->currentUser()->isClient()){
			$mp = $this->add('Model_Project')->notDeleted()->forClient();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
		}elseif($this->api->currentUser()->isDeveloper()){
			$mp = $this->add('Model_Project')->notDeleted()->participateIn();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
		}else{
			$mp = $this->add('Model_Project')->notDeleted();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
		}

		//$this->addField('project_id')->refModel('Model_Project');
		//->display(array('form'=>'autocomplete/basic'));
		$this->hasOne('User');
		$this->addField('name')->mandatory('required');
		$this->addField('general_description')->type('text')->allowHtml(true);
		$this->addField('amount')->type('money')->mandatory(true);
		$this->addField('issued')->type('date');

		$this->addField('duration')->type('int');
		$this->addField('deadline')->type('date')->caption('Duration/Deadline');

		$this->addExpression('durdead')->caption('Duration(days)/Deadline')->set(function($m,$q){
			return $q->dsql()
				->expr('if(deadline is null,duration,deadline)');
		});

		$this->addField('html')->type('text')->allowHtml(true);

		$this->addField('status')->setValueList(
			array(
				'quotation_requested'=>'Quotation Requested',
				'estimate_needed'=>'Estimate Needed',
				'not_estimated'=>'Not Estimated',
				'estimated'=>'Estimated',
				'estimation_approved'=>'Estimation Approved',
				'finished'=>'Finished',
			)
		)->mandatory('Cannot be empty')->sortable(true);
		//$this->addField('attachment_id')->setModel('Model_Filestore_File');

		$this->addField('rate')->defaultValue('0.00');
		$this->addField('currency')->setValueList(
			array(
				'GBP'=>'GBP',
				'EUR'=>'EUR',
				'USD'=>'USD',
			)
		)->mandatory('Cannot be empty');

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');
		});

//        $this->addField('organisation_id')->refModel('Model_Organisation');
		$this->hasOne('Organisation','organisation_id');

		$this->addField('created_dts');
		$this->addField('updated_dts')->caption('Updated')->sortable(true);

		$this->addField('expires_dts')->caption('Expires');

		$this->addField('is_archived')->type('boolean')->defaultValue('0');

		$this->addField('warranty_end')->type('date')->caption('Warranty end');

		$this->addField('show_time_to_client')->type('boolean')->defaultValue('0');

		$this->addExpressions();

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeInsert', function($m,$q){
			$q->set('created_dts', $q->expr('now()'));
			$q->set('expires_dts', $q->expr('DATE_ADD(NOW(), INTERVAL 1 MONTH)'));
		});

		$this->addHook('beforeSave', function($m){
			$m['updated_dts']=date('Y-m-d G:i:s', time());
			if($m['status']=='finished') $m['warranty_end']=date('Y-m-d G:i:s', time()+60*60*24*30);
		});
	}

	function addExpressions(){
		$this->addExpression('client_id')->set(function($m,$q){
			return $q->dsql()
				->table('project')
				->field('client_id')
				->where('project.id',$q->getField('project_id'))
				;
		});

		$this->addExpression('estimated')->caption('Est.time(hours)')->set(function($m,$q){
			return $q->dsql()
				->table('requirement')
				->field('sum(estimate)')
				->where('requirement.quote_id',$q->getField('id'))
				->where('requirement.is_included','1')
				;
		});

		$this->addExpression('calc_rate')->caption('Rate')->set(function($m,$q){
			return 'IF( rate
                    ,
                        rate
                    ,
                        IF(
                            (SELECT value FROM rate WHERE
                                `from`<=(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                                AND
                                `to`>(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                            )
                        ,
                            (SELECT value FROM rate WHERE
                                `from`<=(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                                AND
                                `to`>(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                            )
                        ,
                        ""
                        )
                    )
                    ';
		});

		$this->addExpression('estimpay')->caption('Est.pay')->set(function($m,$q){
			return 'IF(
                        (SELECT SUM(requirement.estimate)*quote.rate
                        FROM requirement
                        WHERE requirement.quote_id=quote.id
                            AND requirement.is_included=1)
                    ,
                        (SELECT SUM(requirement.estimate)*quote.rate
                        FROM requirement
                        WHERE requirement.quote_id=quote.id
                            AND requirement.is_included=1)
                    ,   IF(
                            (SELECT SUM(requirement.estimate)*
                                (SELECT value FROM rate WHERE
                                `from`<=(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                                AND
                                `to`>(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                            )
                                FROM requirement
                                WHERE requirement.quote_id=quote.id
                                    AND requirement.is_included=1)
                        ,
                            (SELECT SUM(requirement.estimate)*
                                (SELECT value FROM rate WHERE
                                `from`<=(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                                AND
                                `to`>(SELECT SUM(requirement.estimate)
                                    FROM requirement
                                    WHERE requirement.quote_id=quote.id
                                        AND requirement.is_included=1
                                )
                            )
                                FROM requirement
                                WHERE requirement.quote_id=quote.id
                                    AND requirement.is_included=1)
                        ,
                        ""
                        )
                    )
                    ';
			/*
			return $q->dsql()
				->table('requirement')
				->field('sum(estimate)*'.$q->getField('rate'))
				->where('requirement.quote_id',$q->getField('id'))
				->where('requirement.is_included','1')
				;
			*/
		});

		$this->addExpression('spent_time')->set(function($m,$q){
			return $q->dsql()
				->table('task')
				->table('task_time')
				->table('requirement')
				->field('sum(task_time.spent_time)')
				->where('requirement.id=task.requirement_id')
				->where('task.id=task_time.task_id')
				->where('requirement.quote_id',$q->getField('id'))
				->where('remove_billing',0)
				;
		});
	}

	function deleted() {
		$this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
	function archived(){
		$this->addCondition('is_archived',true);
	}
	function notArchived(){
		$this->addCondition('is_archived',false);
	}
	function getThisOrganisation() {
		$this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
		return $this;
	}
	function participated(){
		$participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->api->auth->model['id']);
		$projects_ids="";
		foreach($participated_in as $p){
			if($projects_ids=="") $projects_ids=$p['project_id'];
			else $projects_ids=$projects_ids.','.$p['project_id'];
		}
		$this->addCondition('project_id','in',$projects_ids);
		return $this;
	}


	function getRequirements(){
		$rm=$this->add('Model_Requirement')->addCondition('quote_id',$this->get('id'));
		return($rm->getRows());
	}
	function getRequirements_id(){
		$rids='';
		foreach($this->getRequirements() as $reqs){
			if ($rids=='') $rids=$reqs['id']; else $rids.=','.$reqs['id'];
		}

		return($rids);
	}

    function in_archive(){
        $this->set('is_archived',true);
    }
    function activate(){
        $this->set('is_archived',false);
    }

    function approve() {
        $not_included_requirements = $this->add('Model_Requirement') //->debug()
                ->addCondition('quote_id',$this->id)
                ->addCondition('is_included',false)
                ->getRows();

        if (count($not_included_requirements)) {
            $this->moveRequirmsToOtherQuote(
                $not_included_requirements, $this->cloneThisQuote()
            );
        }

        $this->set('status','estimation_approved');
        $this->save();
    }
    function cloneThisQuote() {
        return $this->add('Model_Quote')
            ->set('project_id',$this['project_id'])
            ->set('user_id',$this['user_id'])
            ->set('name',$this['name'].' (not included requirements)')
            ->set('status',$this['status'])
            ->set('currency',$this['currency'])
            ->set('rate',$this['rate'])
            ->set('organisation_id',$this['organisation_id'])
            ->save()
        ;
    }
    function moveRequirmsToOtherQuote($reqs_arr,$other_quote) {
        $req = $this->add('Model_Requirement'); //->debug();
        foreach ($reqs_arr as $req_arr) {
            $req->tryLoad($req_arr['id']);
            if ($req->loaded()) {
                $req
                    ->set('quote_id',$other_quote->id)
                    ->set('is_included',true)
                    ->saveAndUnload();
            }
        }
    }
    function sendEmailToClient() {
       	if ($this['client_id']>0){
       		return $this->add('Model_Client')->notDeleted()->load($this['client_id'])->sendQuoteEmail($this->id);
       	} else {
            throw $this->exception('The project of this quote has no client!','Exception_QuoteHasNoClient');
       	}
    }



    /* **********************************
     *
     *      QUOTE ACCESS RULES
     *
     */
    // check if this user can change 'is_included' flag of requirement
    function canUserChangeIsIncluded($user) {
        $cannot_toggle_statuses = array('estimation_approved','finished',);

        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        } else if ($user->isDeveloper()) {
            return false;
        } else if ($user->isClient()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        } else if ($user->isSales()) {
            if ( !in_array($this['status'],$cannot_toggle_statuses) ) {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    // ONLY manager and client have access to quote price
    function canUserSeePrice() {
        return $this->app->user_access->checkRoleSimpleRights(array(false,true,false,true,false));
    }

    function canUserSeeRequirements($user) {
    }

    function canUserSeeQuote($user) {
    }

    function canUserDeleteRequirement($user) {
        return $this->app->user_access->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserDeleteQuote($user) {
        return $this->app->user_access->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserAddQuote($user) {
        return $this->app->user_access->canSendRequestForQuotation();
    }

    // ONLY developer have access to estimate quotes with status 'estimate_needed'
    function canUserEstimateQuote($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return false;
        } else if ($user->isDeveloper()) {
            if ($this['status']=='estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            return false;
        } else if ($user->isSales()) {
            return false;
        }
        throw $this->exception('Wrong role');
    }

    // ONLY client and manager have access to estimate quotes with status 'estimated'
    function canUserApproveQuote($user) {
        if ( ($user->isManager()) || ($user->isClient()) ) {
            if ($this['status']=='estimated') {
                return true;
            }
        }
        return false;
    }

    function canUserEditQuote($user) {
        return $this->app->user_access->checkRoleSimpleRights(array(false,true,false,false,false));
    }

    function canUserRequestForEstimate($user) {
        // manager can send request for estimate if status 'quotation_requested' or 'not_estimated'
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            if ( $this['status']=='quotation_requested' || $this['status']=='not_estimated' ) {
                return true;
            }
            return false;
        } else if ($user->isDeveloper()) {
            return false;
        } else if ($user->isClient()) {
            return false;
        } else if ($user->isSales()) {
            return true;
        }
        throw $this->exception('Wrong role');
    }

    function canUserReadRequirements($user) {
        if ($user->isAdmin()) {
            return true;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            if ($this['status'] != 'quotation_requested') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            // TODO !!!!!  ~~>  client have access to quotes of its projects ONLY!
            return true;
        } else if ($user->isSales()) {
            return true;
        }
        throw $this->exception('Wrong role');
    }

    function canUserEditRequirements($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            if ($this['status'] == 'estimate_needed') {
                return true;
            }
            return false;
        } else if ($user->isClient()) {
            if ($this['status']=='quotation_requested' || $this['status']=='not_estimated') {
                return true;
            }
            return false;
        } else if ($user->isSales()) {
            if ($this['status']=='quotation_requested' || $this['status']=='not_estimated') {
                return true;
            }
            return false;
        }
        throw $this->exception('Wrong role');
    }

    function whatRequirementFieldsUserCanEdit($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('name','descr','estimate','file_id');
        } else if ($user->isDeveloper()) {
            return array('estimate');
        } else if ($user->isClient()) {
            return array('name','descr','file_id');
        } else if ($user->isSales()) {
            return array('name','descr','file_id');
        }
        throw $this->exception('Wrong role');
    }

    function whatRequirementFieldsUserCanSee($user) {
        if ($user->isAdmin()) {
            return false;
        } else if ($user->isManager()) {
            return array('is_included','name','estimate','cost','spent_time','file','user','count_comments');
        } else if ($user->isDeveloper()) {
            return array('is_included','name','estimate','spent_time','file','user','count_comments');
        } else if ($user->isClient()) {
            if($this['show_time_to_client']){
                return array('is_included','name','cost','estimate','spent_time','file','count_comments');
            }else{
                return array('is_included','name','cost','file','count_comments');
            }
        } else if ($user->isSales()) {
            return array('is_included','name','cost','estimate','spent_time','file','count_comments');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanSee($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('id','project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status','warranty_end','show_time_to_client','updated_dts','expires_dts');
        } else if ($user->isDeveloper()) {
            return array('id','project','user','name','estimated','spent_time','durdead','status','warranty_end','updated_dts','expires_dts');
        } else if ($user->isClient()) {
            return array('id','project','name','estimated','spent_time','estimpay','rate','currency','durdead','status','warranty_end','updated_dts','expires_dts','show_time_to_client');
        } else if ($user->isSales()) {
            return array('id','project','name','estimated','spent_time','estimpay','rate','currency','durdead','status','warranty_end','updated_dts','expires_dts');
        }
        throw $this->exception('Wrong role');
    }

    function whatQuoteFieldsUserCanEdit($user) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isFinancial()) {
            return array('name','project_id','general_description','rate','currency','duration','deadline','status','warranty_end','show_time_to_client','expires_dts');
        } else if ($user->isManager()) {
            return array('name','project_id','general_description','duration','deadline','status','warranty_end','expires_dts');
        } else if ($user->isDeveloper()) {
            return array();
        } else if ($user->isClient()) {
            return array();
        } else if ($user->isSales()) {
            return array();
        }
        throw $this->exception('Wrong role');
    }

    function userAllowedActions($user,$mode) {
        if ($user->isAdmin()) {
            return array();
        } else if ($user->isManager()) {
            return array('requirements','estimation','send_to_client','approve',$mode,);
        } else if ($user->isDeveloper()) {
            return array('details','estimate',$mode,);
        } else if ($user->isClient()) {
            return array('details','edit_details','approve',);
        } else if ($user->isSales()) {
            return array('details','edit_details',);
        }
        throw $this->exception('Wrong role');
    }

    function canStatusBeChangedToEstimated($requirements=null) {
        if (!$this->canUserEstimateQuote($this->api->currentUser()))
            throw $this->exception('User with this role cannot estimate quote.','Exception_Denied');

        if (!$requirements) {
            $requirements = $this->add('Model_Requirement');
            $requirements->addCondition('quote_id',$this->id);
        }

        foreach ($requirements as $requirement){
            if($requirement['is_included'] && (($requirement['estimate']==null) || ($requirement['estimate']==0))) {
                return false;
            }
        }
        return true;
    }
    function showExpiredBox(){
        if($this->get('status')!='estimation_approved' && $this->get('status')!='finished'){
            return true;
        }
        return false;
    }
    function isExpired(){
        if($this->get('status')!='estimation_approved' && $this->get('status')!='finished'){
            if (strtotime($this->get('expires_dts'))<time()){
                return true;
            }
        }
        return false;
    }
    function userAllowedArchive($user) {
        if ($user->isAdmin()) {
            return true;
        } else if ($user->isManager()) {
            return true;
        } else if ($user->isDeveloper()) {
            return true;
        } else if ($user->isClient()) {
            return false;
        } else if ($user->isSales()) {
            return true;
        }
        throw $this->exception('Wrong role');
    }


}

class Form_Field_AutoEmpty extends autocomplete\Form_Field_Basic {
	public $min_length = -1;
	public $hint = 'Ckick to see list of projects. Search results will be limited to 20 records.';
	function init(){
		parent::init();
		$this->other_field->js('click',array(
				$this->other_field->js()->autocomplete( "search", "" ),
			)
		);
	}
}
