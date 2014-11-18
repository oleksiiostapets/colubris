<?php
class Model_Quote extends Model_Auditable {
	public $table="quote";
	function init(){
		parent::init(); //$this->debug();
/*TODO: check by API. Temporary commented till fix
		if ($this->api->currentUser()->isClient()){
			$mp = $this->add('Model_Project')->notDeleted()->forClient();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
		}elseif($this->api->currentUser()->isDeveloper()){
			$mp = $this->add('Model_Project')->notDeleted()->participateIn();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
		}else{
			$mp = $this->add('Model_Project')->notDeleted();
			$this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');

*/
        //-----TEMPORARY----------TODO:
        $mp = $this->add('Model_Project')->notDeleted();
        $this->hasOne($mp)->display(array('form'=>'Form_Field_AutoEmpty'))->mandatory('required');
        //TEMPORARY----------

		//$this->addField('project_id')->refModel('Model_Project');
		//->display(array('form'=>'autocomplete/basic'));
		$this->hasOne('User');
		$this->addField('name')->mandatory('required');
		$this->addField('general_description')->type('text')->allowHtml(true);
		$this->addField('amount')->type('money')->mandatory(true);
		$this->addField('issued')->type('date');

		$this->addField('duration')->type('int');
		$this->addField('deadline')->type('date')->caption('Duration/Deadline');

		$this->addExpression('durdead')->caption('Duration (days) / Deadline')->set(function($m,$q){
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
		$this->hasOne('User','deleted_id');

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
	//                          HOOKS
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
        $this->addHook('beforeDelete', function($m){
            if( !isset($this->app->is_test_app)) $m['deleted_id']=$m->api->currentUser()->get('id');
        });
	}
	// HOOKS --------------------------------------------------------------------------


	// ------------------------------------------------------------------------------
	//
	//                          Expressions
	//
	// ------------------------------------------------------------------------------
	function addExpressions(){
		$this->addExpression('client_id')->set(function($m,$q){
			return $q->dsql()
				->table('project')
				->field('client_id')
				->where('project.id',$q->getField('project_id'))
				;
		});
		$this->addExpression('client_name')->set(function($m,$q){
			return $q->dsql()
                ->table('client')
                ->table('project')
                ->field('client.name')
                ->where('client.id=project.client_id')
                ->where('project.id',$q->getField('project_id'))
				;
		});
		$this->addExpression('client_email')->set(function($m,$q){
			return $q->dsql()
                ->table('client')
                ->table('project')
                ->field('client.email')
                ->where('client.id=project.client_id')
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
            $m_r = $this->add('Model_Requirement')
                ->addCondition('quote_id',$m->getField('id'))
                ->fieldQuery('id');
            $m_t = $this->add('Model_Task')
                ->addCondition('requirement_id','in',$m_r)
                ->addCondition('project_id',$m->getField('project_id'))
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
	function archived(){
		$this->addCondition('is_archived',true);
	}
	function notArchived(){
		$this->addCondition('is_archived',false);
	}
	function getThisOrganisation() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		return $this;
	}
	function participated(){
		$participated_in=$this->add('Model_Participant')->addCondition('user_id',$this->app->currentUser()->get('id'));
		$projects_ids="";
		foreach($participated_in as $p){
			if($projects_ids=="") $projects_ids=$p['project_id'];
			else $projects_ids=$projects_ids.','.$p['project_id'];
		}
		$this->addCondition('project_id','in',$projects_ids);
		return $this;
	}


	function getRequirements(){
		$rm=$this->add('Model_Requirement')->notDeleted()->addCondition('quote_id',$this->get('id'));
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
        $req = $this->add('Model_Requirement')->notDeleted(); //->debug();
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

    function canStatusBeChangedToEstimated($requirements=null) {
        if (!$this->canUserEstimateQuote($this->api->currentUser()))
            throw $this->exception('User with this role cannot estimate quote.','Exception_Denied');

        if (!$requirements) {
            $requirements = $this->add('Model_Requirement')->notDeleted();
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

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeQuotes($u['id'])){
            $fields = array('id','name','user_id','general_description','issued','duration','deadline','durdead','html','status','is_deleted','deleted_id','organisation_id','created_dts','updated_dts','expires_dts','is_archived','warranty_end','show_time_to_client','client_id','client_name','client_email','estimated','spent_time');

            if($r->canSeeFinance($u['id'])){
                $fin_fields = array('amount','rate','currency','calc_rate','estimpay');
                $fields = array_merge($fields, $fin_fields);
            }
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddQuote($u['id'])){
            $fields = array('id','name','user_id','general_description','issued','duration','deadline','durdead','html','status','is_deleted','deleted_id','organisation_id','created_dts','updated_dts','expires_dts','is_archived','warranty_end','show_time_to_client','client_id','client_name','client_email','estimated','spent_time');

            if($r->canSeeFinance($u['id'])){
                $fin_fields = array('amount','rate','currency','calc_rate','estimpay');
                $fields = array_merge($fields, $fin_fields);
            }
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

        if($r->canEditQuote($u['id'])){
            $fields = array('id','name','user_id','general_description','issued','duration','deadline','durdead','html','status','is_deleted','deleted_id','organisation_id','created_dts','updated_dts','expires_dts','is_archived','warranty_end','show_time_to_client','client_id','client_name','client_email','estimated','spent_time');

            if($r->canSeeFinance($u['id'])){
                $fin_fields = array('amount','rate','currency','calc_rate','estimpay');
                $fields = array_merge($fields, $fin_fields);
            }
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForDelete(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeQuotes($u['id'])){
            $fields = array('id','name','user_id','general_description','issued','duration','deadline','durdead','html','status','is_deleted','deleted_id','organisation_id','created_dts','updated_dts','expires_dts','is_archived','warranty_end','show_time_to_client','client_id','client_name','client_email','estimated','spent_time');

            if($r->canSeeFinance($u['id'])){
                $fin_fields = array('rate','currency','calc_rate','estimpay');
                $fields = array_merge($fields, $fin_fields);
            }
        }

        $this->setActualFields($fields);
        return $this;
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
