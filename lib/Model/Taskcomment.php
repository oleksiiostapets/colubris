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
       		$to='';
        	if ($task->get('requester_id')>0){
        		$u=$m->add('Model_User')->load($task->get('requester_id'));
        		if ($u['email']!='') $to=$u['email'];
        	}
        	if ($task->get('assigned_id')>0){
        		$u=$m->add('Model_User')->load($task->get('assigned_id'));
        		if ($u['email']!=''){
        			if ($to=='') $to=$u['email']; else $to.=', '.$u['email'];
        		}
        	}
        	if ($to!=''){
        		$m->api->mailer->sendMail($to,'task_comment_changed',array(
        				'link'=>$m->api->url('/'.$m->api->getUserType().'/tasks'),
        				'task_name'=>$task->get('name'),
        				));
        	}
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
