<?
class Model_Attach extends Model_Table {
	public $table='attach';
	function init(){
		parent::init();
		$this->hasOne('Task');
		$this->addField('description')->type('text');
		$attach = $this->add('filestore/Field_Image','file_id')->setModel('ImageOrFile')->mandatory(true);
        $attach->addThumb();

        $this->addField('created_dts');
        $this->addField('updated_dts');
        
        $this->addHook('beforeInsert', function($m,$q){
        	$q->set('created_dts', $q->expr('now()'));
        });
        
       	$this->addHook('beforeSave', function($m){
       		$m['updated_dts']=date('Y-m-d G:i:s', time());
       	
       		$task=$m->add('Model_Task')->load($m->get('task_id'));
            $m->api->mailer->task_status=$task['status'];
            $m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_attachment_changed',array(
                    'link'=>$m->api->siteURL().$m->api->url('/tasks'),
                    'task_name'=>$task->get('name'),
                    ));
       	});
        
       	$this->addHook('beforeDelete', function($m){
       		$task=$m->add('Model_Task')->load($m->get('task_id'));
            $m->api->mailer->task_status=$task['status'];
            $m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_attachment_deleted',array(
                    'link'=>$m->api->siteURL().$m->api->url('/tasks'),
                    'task_name'=>$task->get('name'),
                    ));
       	});
        
       	$this->setOrder('updated_dts',true);
	}
}
