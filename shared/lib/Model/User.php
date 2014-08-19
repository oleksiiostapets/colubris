<?php
class Model_User extends Model_BaseTable {
	public $table='user';
    function init(){
        parent::init(); //$this->debug();
		if (@$this->app->auth) $this->app->auth->addEncryptionHook($this);

		// fields
		$this->addField('email')->mandatory('required');
		$this->addField('name');
		$this->addField('password')->display(array('form'=>'password'))->mandatory('required');
		$this->addField('is_admin')->type('boolean');
		$this->addField('is_manager')->type('boolean');
		$this->addField('is_financial')->type('boolean')->caption('Is Financial Manager');
		$this->addField('is_developer')->type('boolean')->caption('Is Team Member');
		$this->addField('is_sales')->type('boolean')->caption('Is Sales Manager');
		$this->addField('hash');
		$this->addField('mail_task_changes')->type('boolean')->caption('Send when task changed');
		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('avatar_id');
		$this->hasOne('User','deleted_id');
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->app->currentUser()->get('id');
		});

		$this->addField('is_system')->defaultValue('0')->type('boolean');
		$this->hasOne('Client');

		$this->addField('chash');

		$this->hasOne('Organisation')->mandatory('required');

        // For logging through APIs
        $this->addField('lhash');
        $this->addField('lhash_exp');

        // expressions
		$this->addExpression('is_client')->datatype('boolean')->set(function($m,$q){
			return $q->dsql()
				->expr('if(client_id is null,false,true)');
		});
        $this->addExpression('avatar')->set(function($m,$q){
            return $q->dsql()
                ->table('filestore_file')
                ->field('filename')
                ->where('filestore_file.id',$q->getField('avatar_id'))
                ;
        });
        $this->addExpression('avatar_thumb')->set(function($m,$q){
            return $q->dsql()
                ->table('filestore_file')
                ->table('filestore_image')
                ->field('filename')
                ->where('filestore_image.original_file_id',$q->getField('avatar_id'))
                ->where('filestore_image.thumb_file_id=filestore_file.id')
                ;
        });

		// order
		$this->setOrder('name');


		$this->addHooks();
    }

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeInsert',function($m){
			if($m->getBy('email',$m['email'])) throw $m
				->exception('User with this email already exists','ValidityCheck')
				->setField('email');
		});

		$this->addHook('beforeModify',function($m){
			if($m->dirty['email']) throw $m
				->exception('Do not change email for existing user','ValidityCheck')
				->setField('email');
		});
	}

	// HOOKS :: END -----------------------------------------------------------


	function getActive(){
		$this->addCondition('is_system',false);
		$this->addCondition('is_deleted',false);
		return $this;
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
	function getAdmins(){
		$this->addCondition('is_admin',true);
		return $this;
	}
	function getDevelopers(){
		$this->addCondition('is_developer',1);
		return $this;
	}
	function getUsersOfOrganisation(){
		$this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
		return $this;
	}
	function getSystemUsers(){
		$this->addCondition('is_system',true);
		$this->addCondition('is_deleted',false);
		return $this;
	}

	function me(){
		$this->addCondition('id',$this->app->auth->get('id'));
		return $this;
	}
	function beforeInsert(&$d){
		$d['hash']=md5(uniqid());
		return parent::beforeInsert($d);
	}
	function resetPassword(){
		throw $this->exception('Function resetPassword is not implemented yet');
	}

    // For APIs
    function setLHash(){
        $this->set('lhash',md5(time().$this->get('password')));
        $this->set('lhash_exp',date('Y-m-d G:i:s', time() + $this->app->getConfig('api_login_expire_minutes') * 60));
        $this->save();
        return array('lhash' => $this->get('lhash'), 'lhash_exp' => $this->get('lhash_exp'));
    }

    function checkUserByLHash($lhash){
        $this->addCondition('lhash_exp','>',date('Y-m-d G:i:s', time()));
        $this->tryLoadBy('lhash',$lhash);
        if($this->loaded()) return true; else return false;
    }



	/* **********************************
	 *
	 *      GET RELATED MODELS
	 *
	 */
	function getDashboardCommentsModel($type) {
		$type_low = strtolower($type);
		if ($this->isClient()) {
			$m = $this->add('Model_'.$type.'comment')->notDeleted()->getClients();
		} elseif ($this->app->currentUser()->isDeveloper()) {
			$m = $this->add('Model_'.$type.'comment')->notDeleted()->getDevelopers();
		} else {
			$m = $this->add('Model_'.$type.'comment')->notDeleted();
		}
		//$m->debug();
		$m->addCondition('user_id','<>',$this['id']);

		$m->setOrder('created_dts',true);

		$proxy_check = $this->add('Model_'.$type.'commentUser');
		$proxy_check->addCondition('user_id',$this['id']);
		$proxy_check->_dsql()->field($type_low.'comment_id');
		$m->addCondition('id','NOT IN',$proxy_check->_dsql());

		switch ($type) {
			case 'Req':
				$jr = $m->join('requirement.id','requirement_id','left','_req');
				$jr->addField('requirement_name','name');
				$jr->addField('quote_id','quote_id');
				//$jr->addField('requirement_id','id');

				$jq = $jr->join('quote.id','quote_id','left','_quote');
				$jq->addField('quote_name','name');
				$jq->addField('quote_status','status');

				$jp = $jq->join('project.id','project_id','left','_pr');
				$jp->addField('project_name','name');
				$jp->addField('organisation_id','organisation_id');
				$m->addCondition('organisation_id',$this['organisation_id']);

				$m->addCondition('quote_status','IN',array('quotation_requested','estimate_needed','not_estimated','estimated'));
				break;
			case 'Task':
				$jt = $m->join('task.id','task_id','left','_t');
				$jt->addField('task_name','name');

				$jp = $jt->join('project.id','project_id','left','_pr');
				$jp->addField('project_name','name');
				$jp->addField('organisation_id','organisation_id');
				$m->addCondition('organisation_id',$this['organisation_id']);
				break;
			default:
				throw $this->exception('There is no such a type: '.$type);
		}
		return $m;
	}



	/* *********************************
	 *
	 *             GET ROLES
	 *
	 */
	function canBeAdmin() {
		return ($this['is_admin']?true:false);
	}
	function canBeDeveloper() {
		return ($this['is_developer']?true:false);
	}
	function canBeClient() {
		return ($this['is_client']?true:false);
	}
	function canBeManager() {
		return ($this['is_manager']?true:false);
	}
	function canBeSales() {
		return ($this['is_sales']?true:false);
	}
	function canBeSystem() {
		return ($this['is_system']?true:false);
	}


	function canSeeFinance() { // canSeeFinance
		return ($this['is_financial']?true:false);
	}



	/* **********************************
	 *
	 *          CURRENT USER ROLE
	 *
	 */
	function isSystem() {
		return ($this->app->getCurrentUserRole() == 'system');
	}
	function isAdmin() {
		return ($this->app->getCurrentUserRole() == 'admin');
	}
	function isFinancial() {
		return ($this->app->auth->model['is_financial']);
	}
	function isManager() {
		return ($this->app->getCurrentUserRole() == 'manager');
	}
	function isSales() {
		return ($this->app->getCurrentUserRole() == 'sales');
	}
	function isDeveloper() {
		return ($this->app->getCurrentUserRole() == 'developer');
	}
	function isClient() {
		return ($this->app->getCurrentUserRole() == 'client');
	}

}
