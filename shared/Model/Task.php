<?php
class Model_Task extends Model_Auditable {
    public $table='task';
    function init(){
        parent::init(); //$this->debug();

        $this->addField('name')->mandatory(true);
        $this->addField('priority')->setValueList(
            array(
                'low'=>'low',
                'normal'=>'normal',
                'high'=>'high',
            )
        )->defaultValue('normal');

        $this->addField('status')->setValueList($this->api->task_statuses)->defaultValue('unstarted')->sortable(true);
        $this->addField('type')->setValueList($this->api->task_types)->defaultValue('change request')->sortable(true);

        $this->addField('descr_original')->dataType('text');

        $this->addField('estimate')->dataType('money');
        //$this->addField('spent_time')->dataType('int');

        //$this->addField('deviation')->dataType('text');

//        $this->addField('project_id')->refModel('Model_Project')->mandatory(true)->sortable(true);
        $this->hasOne('Project','project_id');
        $this->getField('project_id')->mandatory(true)->sortable(true);

//        $this->addField('requirement_id')->refModel('Model_Requirement');
        $this->hasOne('Requirement','requirement_id');
        //$this->addField('requester_id')->refModel('Model_User_Organisation');
        //$this->addField('assigned_id')->refModel('Model_User_Organisation');

        if($this->api->currentUser()->isClient()){
            /* Doesn't work with deleting tasks in CRUD
                        $j = $this->join('project.id','project_id','left','_p');
                        $j->addField('client_id','client_id');
                        $this->addCondition('client_id',$this->api->auth->model['client_id']);
            */
            $mp=$this->add('Model_Project');
            $mp->forClient();
            $projects_ids="0";
            foreach($mp->getRows() as $p){
                $projects_ids=$projects_ids.','.$p['id'];
            }
            $this->addCondition('project_id','in',$projects_ids);
        }

        if($this->api->currentUser()->isDeveloper()){
            $mp=$this->add('Model_Project');
            $mp->forDeveloper();
            $projects_ids="0";
            foreach($mp->getRows() as $p){
                $projects_ids=$projects_ids.','.$p['id'];
            }
            $this->addCondition('project_id','in',$projects_ids);
        }

        $this->addField('created_dts');
        $this->addField('updated_dts')->caption('Updated')->sortable(true);

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
        $this->hasOne('User','deleted_id');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });

//        $this->addField('organisation_id')->refModel('Model_Organisation');
        $this->hasOne('Organisation','organisation_id');
        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        $this->addHook('beforeInsert', function($m,$q){
            $q->set('created_dts', $q->expr('now()'));
        });

        $this->addHook('beforeSave', function($m){
            $m['updated_dts']=date('Y-m-d G:i:s', time());
        });
        $this->addHook('afterSave', function($m){
            $m->api->mailer->task_status=$m['status'];
            $m->api->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_edit',array(
                'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('id'),'colubris_task_view_view_crud_virtualpage'=>null)),
                'subject'=>'Task "'.substr($m->get('name'),0,25).'" has changes',
                'changer_part'=>$m->api->currentUser()->get('name').' has made changes in task "'.$m->get('name').'".',
            ));
        });
        $this->addHook('beforeDelete', function($m){
            $m->api->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_delete',array(
                'subject'=>'Task "'.substr($m->get('name'),0,25).'" deleted',
                'changer_part'=>$m->api->currentUser()->get('name').' has deleted task "'.$m->get('name').'".',
            ));
        });

        $this->setOrder('updated_dts',true);

        $this->addExpression('spent_time')->set(function($m,$q){
            return $q->dsql()
                ->table('task_time')
                ->field('sum(task_time.spent_time)')
                ->where('task_time.task_id',$q->getField('id'))
                ->where('task_time.remove_billing',false)
                ;
        });

        $this->addCondition('is_deleted',false);
    }
    function addDashCondition() {
        if (!$_GET['submit']) {
            $this->addCondition('status','<>','accepted');
        }
        $this->_dsql()->where(
            $this->_dsql()->orExpr()
                ->where('requester_id',$this->app->auth->model['id'])
                ->where('assigned_id',$this->app->auth->model['id'])
        );
        return $this;
    }

    // Model_Task_Base
    function Base() {
//        $this->addField('requester_id')->refModel('Model_User_Organisation');
        $this->hasOne('User_Organisation','requester_id');
//        $this->addField('assigned_id')->refModel('Model_User_Organisation');
        $this->hasOne('User_Organisation','assigned_id');
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
    function addQuoteId() {
        $this->addExpression('quote_id',function($m,$q){
            $req = $m->add('Model_Requirement')->addCondition('id',$m->getElement('requirement_id'));
            $quote = $m->add('Model_Quote')->addCondition('id',$req->fieldQuery('quote_id'));
            return $quote->fieldQuery('id');
        });
    }
    function addQuoteName() {
        $this->addExpression('quote',function($m,$q){
            $req = $m->add('Model_Requirement')->addCondition('id',$m->getElement('requirement_id'));
            $quote = $m->add('Model_Quote')->addCondition('id',$req->fieldQuery('quote_id'));
            return $quote->fieldQuery('name');
        });
    }
}
