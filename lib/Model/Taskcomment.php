<?
class Model_Taskcomment extends Model_Table {
	public $table='taskcomment';
	function init(){
		parent::init();
		$this->hasOne('Task');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('text')->type('text')->mandatory('required');
		
		$this->add('filestore/Field_File', array(
				'name'=>'file_id',
				'use_model'=>'Model_Myfile'
		));
		
		$this->addField('created_dts')->Caption('Created At')->sortable(true);
		
		$this->addHook('beforeInsert',function($m,$q){
			$q->set('user_id',$q->api->auth->model['id']);
        	$q->set('created_dts', $q->expr('now()'));
		});
		
        $this->addHook('beforeSave',function($m){
        	if($m['user_id']>0){
	        	if($m->api->auth->model['id']!=$m['user_id']){
	        		throw $m
		                ->exception('You have no permissions to do this','ValidityCheck')
		                ->setField('text');
	        	}
        	}
        	
			$task=$m->add('Model_Task')->load($m->get('task_id'));
            $m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_comment_changed',array(
                    'link'=>$m->api->siteURL().$m->api->url($m->api->getUserType().'/tasks'),
                    'task_name'=>$task->get('name'),
                    ));
        });
        $this->addHook('beforeDelete',function($m){
        	if($m['user_id']>0){
	        	if($m->api->auth->model['id']!=$m['user_id']){
	        		throw $m
		                ->exception('You have no permissions to do this','ValidityCheck');
	        	}
        	}
        });
	}
}
