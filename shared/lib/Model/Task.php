<?php
class Model_Task extends Model_Auditable {
    public $table='task';
    public $task_statuses = array(
        'unstarted'=>'unstarted',
        'started'=>'started',
        'finished'=>'finished',
        'tested'=>'tested',
        'rejected'=>'rejected',
        'accepted'=>'accepted',
    );
    public static $task_types = array(
        'project'=>'Project',
        'change_request'=>'Change request',
        'bug'=>'Bug',
        'support'=>'Support',
        'drop'=>'Drop',
    );
    public static $task_priority = array(
        'low'=>'low',
        'normal'=>'normal',
        'high'=>'high',
    );
    function init(){
        parent::init(); //$this->debug();

        $this->addField('name')->mandatory(true);
        $this->addField('priority')->setValueList(Model_Task::$task_priority)->defaultValue('normal');

        $this->addField('status')->setValueList($this->task_statuses)->defaultValue('unstarted')->sortable(true);
        $this->addField('type')->setValueList(Model_Task::$task_types)->defaultValue('change_request')->sortable(true);

        $this->addField('descr_original')->dataType('text');

        $this->addField('estimate')->dataType('money');
        $this->hasOne('Project','project_id');
        $this->getField('project_id')->mandatory(true)->sortable(true);

        $this->hasOne('Requirement','requirement_id');

		$this->hasOne('User','requester_id');
		$this->hasOne('User','assigned_id');

        $this->addField('created_dts');
        $this->addField('updated_dts')->caption('Updated')->sortable(true);

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->hasOne('User','deleted_id');

        $this->hasOne('Organisation','organisation_id');
//        $this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));

        $this->setOrder('updated_dts',true);

        $this->addSpentTime();

        // expressions
        $this->addExpression('quote_id')->set(function($m,$q){
            return $q->dsql()
                ->expr('if(requirement_id is null,"",
                            (SELECT id FROM quote WHERE quote.id=(SELECT quote_id FROM requirement WHERE requirement.id=task.requirement_id))
                )');
        });
        $this->addExpression('quote_name')->set(function($m,$q){
            return $q->dsql()
                ->expr('if(requirement_id is null,"",
                            (SELECT name FROM quote WHERE quote.id=(SELECT quote_id FROM requirement WHERE requirement.id=task.requirement_id))
                )');
        });

        $this->addHooks();

        //$this->addField('spent_time')->dataType('int');
        //$this->addField('deviation')->dataType('text');
        //$this->addField('project_id')->refModel('Model_Project')->mandatory(true)->sortable(true);
        //$this->addField('requirement_id')->refModel('Model_Requirement');
        //$this->addField('requester_id')->refModel('Model_User_Organisation');
        //$this->addField('assigned_id')->refModel('Model_User_Organisation');
        //$this->addField('deleted_id')->refModel('Model_User');
        //$this->addField('organisation_id')->refModel('Model_Organisation');

    }




    // ------------------------------------------------------------------------------
    //
    //            HOOKS :: BEGIN
    //
    // ------------------------------------------------------------------------------

    function addHooks() {
        $this->addHook('beforeInsert', function($m,$q){
            $q->set('created_dts', $q->expr('now()'));
        });

        $this->addHook('beforeSave', function($m){
            $m['updated_dts']=date('Y-m-d G:i:s', time());
        });
        //TODO I commented it because saving of the task doesn't work with it. Kostya
        /*$this->addHook('afterSave', function($m){
            $m->app->mailer->task_status=$m['status'];
            $m->app->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->app->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->app->mailer->sendMail('task_edit',array(
                'link'=>$m->app->siteURL().$m->app->url('/task',array('task_id'=>$m->get('id'),'colubris_task_view_view_crud_virtualpage'=>null)),
                'subject'=>'Task "'.substr($m->get('name'),0,25).'" has changes',
                'changer_part'=>$m->app->currentUser()->get('name').' has made changes in task "'.$m->get('name').'".',
            ));
        });*/
        /*$this->addHook('beforeDelete', function($m){
            $m->app->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->app->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->app->mailer->sendMail('task_delete',array(
                'subject'=>'Task "'.substr($m->get('name'),0,25).'" deleted',
                'changer_part'=>$m->app->currentUser()->get('name').' has deleted task "'.$m->get('name').'".',
            ));
        });*/
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->app->currentUser()->get('id');
        });
    }

    // HOOKS :: END -----------------------------------------------------------




    // ------------------------------------------------------------------------------
    //
    //            PREPARED CONDITIONS SETS :: BEGIN
    //
    // ------------------------------------------------------------------------------

    public function forTaskCRUD() {
        $this->Base();
	    $this->addQuoteId();
        $this->addQuoteName();
        $this->addGetConditions();
        $this->notDeleted();
	    //$this->debug();
        return $this;
    }
    function addDashCondition() {
        if (!$_GET['submit']) {
            $this->addCondition('status','<>','accepted');
        }
        $this->_dsql()->where(
            $this->_dsql()->orExpr()
                ->where('requester_id',$this->app->currentUser()->get('id'))
                ->where('assigned_id',$this->app->currentUser()->get('id'))
        );
        $this->notDeleted();
        return $this;
    }
	public function forTaskForm(){
		$this->addQuoteId();
	}
    public function restrictedUsers() {
        $this->notDeleted();
        //$this->addField('requester_id')->refModel('Model_User_Task');
        //$this->hasOne('User_Task','requester_id');
        //$this->addField('assigned_id')->refModel('Model_User_Task');
        //$this->hasOne('User_Task','assigned_id');
        return $this;
    }

    // PREPARED CONDITIONS SETS :: END -----------------------------------------------------------






    // ------------------------------------------------------------------------------
    //
    //            CONDITIONS :: BEGIN
    //
    // ------------------------------------------------------------------------------

    function deleted() {
        $this->addCondition('is_deleted',true);
        return $this;
    }
    function notDeleted() {
        $this->addCondition('is_deleted',false);
        return $this;
    }
    // Model_Task_Base
    function Base() {
        //$this->addField('requester_id')->refModel('Model_User_Organisation');
		$mu = $this->add('Model_User')->getUsersOfOrganisation();
        //$this->hasOne($mu,'requester_id');
        //$this->addField('assigned_id')->refModel('Model_User_Organisation');
        //$this->hasOne($mu,'assigned_id');
        return $this;
    }
    function addGetConditions() {
        if ($_GET['project']) {
            $this->addCondition('project_id',$_GET['project']);
        }
        if ($_GET['quote']) {
            $this->addQuoteId();
            $this->addCondition('quote_id',$_GET['quote']);
        }
        if ($_GET['requirement']) {
            $this->addCondition('requirement_id',$_GET['requirement']);
        }
        if ($_GET['status']) {
            $this->addCondition('status',$_GET['status']);
        }
        if ($_GET['assigned']) {
            $this->addCondition('assigned_id',$_GET['assigned']);
        }
        return $this;
    }

    // CONDITIONS :: END -----------------------------------------------------------






    // ------------------------------------------------------------------------------
    //
    //               EXPRESSIONS :: BEGIN
    //
    // ------------------------------------------------------------------------------

    function addQuoteId() {
	    $this->addExpression('quote_id',function($m){
		    $req = $m->add('Model_Requirement')->notDeleted()->addCondition('id',$m->getElement('requirement_id'));
		    $quote = $m->add('Model_Quote')->notDeleted()->getThisOrganisation()->addCondition('id',$req->fieldQuery('quote_id'));
		    return $quote->fieldQuery('id');
	    });
    }
    function addQuoteName() {
        $this->addExpression('quote',function($m){
            $req = $m->add('Model_Requirement')->notDeleted()->addCondition('id',$m->getElement('requirement_id'));
            $quote = $m->add('Model_Quote')->notDeleted()->getThisOrganisation()->addCondition('id',$req->fieldQuery('quote_id'));
            return $quote->fieldQuery('name');
        });
    }
    function addSpentTime() {
        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task_time')
                ->field('sum(task_time.spent_time)')
                ->where('task_time.task_id',$q->getField('id'))
                ->where('task_time.remove_billing',false)
            ;
        });
    }

    // EXPRESSIONS :: END -----------------------------------------------------------

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canSeeTasks($u['id']) && $r->canSeeProjects($u['id']) && $r->canSeeQuotes($u['id'])){
            $fields = array('id','name','priority','status','type','descr_original','estimate','estimate','project_id',
                'project','requirement_id','requirement','requester_id','requester','assigned_id','assigned','created_dts','updated_dts','is_deleted','deleted_id',
                'organisation_id','spent_time','quote_id','quote_name');
        }else{
            throw $this->exception('This User cannot see tasks','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddTask($u['id'])){
            $fields = array('id','name','priority','status','type','descr_original','estimate','estimate','project_id',
                'requirement_id','requester_id','assigned_id','created_dts','updated_dts','is_deleted','deleted_id',
                'organisation_id','spent_time','quote_id','quote_name');
        }else{
            throw $this->exception('This User cannot add tasks','API_CannotAdd');
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

        if($r->canEditTask($u['id'])){
            $fields = array('id','name','priority','status','type','descr_original','estimate','estimate','project_id',
                'requirement_id','requester_id','assigned_id','created_dts','updated_dts','is_deleted','deleted_id',
                'organisation_id','spent_time','quote_id','quote_name');
        }else{
            throw $this->exception('This User cannot edit tasks','API_CannotEdit');
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

        if($r->canDeleteTask($u['id'])) return $this;

        throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
    }
}