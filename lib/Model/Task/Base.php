<?php
class Model_Task_Base extends Model_Auditable {
    public $table='task';

    function init(){
        parent::init();

        //$this->debug();

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

        $this->addField('project_id')->refModel('Model_Project')->mandatory(true)->sortable(true);
        $this->addField('requirement_id')->refModel('Model_Requirement');
        $this->addField('requester_id')->refModel('Model_User_Organisation');
        $this->addField('assigned_id')->refModel('Model_User_Organisation');

        if($this->api->currentUser()->isCurrentUserClient()){
            $j = $this->join('project.id','project_id','left','_p');
            $j->addField('client_id','client_id');
            $this->addCondition('client_id',$this->api->auth->model['client_id']);
        }

        $this->addField('created_dts');
        $this->addField('updated_dts')->caption('Updated')->sortable(true);

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');

        $this->addField('organisation_id')->refModel('Model_Organisation');
        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        $this->addHook('beforeInsert', function($m,$q){
            $q->set('created_dts', $q->expr('now()'));
        });

        $this->addHook('beforeSave', function($m){
            $m['updated_dts']=date('Y-m-d G:i:s', time());

            $m->api->mailer->task_status=$m['status'];
            $m->api->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_edit',array(
                'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('id'))),
                'subject'=>'Task "'.$m->get('name').'" has changes',
            ));
        });

        $this->addHook('beforeDelete', function($m){
            $m->api->mailer->addReceiverByUserId($m->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($m->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_delete',array(
                'link'=>$m->api->siteURL().$m->api->url('/tasks'),
                'subject'=>'Task "'.$m->get('name').'" deleted',
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
    }
}
