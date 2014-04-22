<?php
class Model_Attach extends Model_Auditable {
	public $table='attach';
	function init(){
		parent::init();
		$this->hasOne('Task');
		$this->addField('description')->type('text');
		$attach = $this->add('filestore/Field_Image','file_id')->setModel('ImageOrFile')->mandatory(true);
		$attach->addThumb();

		$this->addField('created_dts');
		$this->addField('updated_dts');

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

		$this->addHook('beforeInsert', function($m,$q){
			$q->set('created_dts', $q->expr('now()'));
		});

		$this->setOrder('updated_dts',true);

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeSave', function($m){
			$m['updated_dts']=date('Y-m-d G:i:s', time());

			$task=$m->add('Model_Task')->load($m->get('task_id'));
			$m->api->mailer->task_status=$task['status'];
			$m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
			$m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
			$m->api->mailer->sendMail('task_attachment_changed',array(
				'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('task_id'),'colubris_task_view_view_crud_virtualpage_id'=>null,'colubris_task_view_view_crud_virtualpage'=>null)),
				'subject'=>'Attachment was changed in task "'.substr($task->get('name'),0,25).'"',
				'changer_part'=>$m->api->currentUser()->get('name').' has made changes in task "'.$task->get('name').'".',
			));
		});

		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');

			$task=$m->add('Model_Task')->load($m->get('task_id'));
			$m->api->mailer->task_status=$task['status'];
			$m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
			$m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
			$m->api->mailer->sendMail('task_attachment_deleted',array(
				'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('task_id'))),
				'subject'=>'Attachment deleted in task "'.substr($task->get('name'),0,25).'"',
				'changer_part'=>$m->api->currentUser()->get('name').' has deleted attachment in task "'.$task->get('name').'".',
			));
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

}
