<?php
class Model_Taskcomment extends Model_Auditable {
	public $table='taskcomment';
	function init(){
		parent::init();
		$this->hasOne('Task');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('text')->type('text')->mandatory('required');

		$attach = $this->add('filestore/Field_Image','file_id')->setModel('Myfile');
		$attach->addThumb();

		$this->addField('created_dts')->Caption('Created At')->sortable(true);

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
		$this->hasOne('User','deleted_id');

		$this->addHooks();

	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->api->currentUser()->get('id');
		});

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
			$m->api->mailer->task_status=$task['status'];
			$m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
			$m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
			$m->api->mailer->sendMail('task_comment_changed',array(
				'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('task_id'),'colubris_task_view_view_2_crud_virtualpage_id'=>null,'colubris_task_view_view_2_crud_virtualpage'=>null,'colubris_task_view_view_crud_virtualpage'=>null)),
				'subject'=>'Task "'.substr($task->get('name'),0,25).'" has changes in comments',
				'changer_part'=>$m->api->currentUser()->get('name').' has made changes in task "'.$task->get('name').'".',
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

	function deleted() {
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}
	function getClients(){
		$participated_in=$this->add('Model_Project')->notDeleted()->forClient();
		$projects_ids=array(0);
		foreach($participated_in as $p){
			$projects_ids[]=$p['id'];
		}
		$jt = $this->join('task.id','task_id','left','_t');

		$jt->addField('project_id','project_id');

		$this->addCondition('project_id','IN',$projects_ids);
		return $this;
	}
	function getDevelopers(){
		$participated_in=$this->add('Model_Project')->notDeleted()->forDeveloper();
		$projects_ids=array(0);
		foreach($participated_in as $p){
			$projects_ids[]=$p['id'];
		}
		$jt = $this->join('task.id','task_id','left','_t');

		$jt->addField('project_id','project_id');

		$this->addCondition('project_id','IN',$projects_ids);
		return $this;
	}

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddCommentToTask($u['id'])){
            $fields = array('id','requirement_id','user_id','text','file_id','created_dts','is_deleted','deleted_id');
        }else{
            throw $this->exception('This User cannot see comments','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddCommentToTask($u['id'])){
            $fields = array('id','requirement_id','user_id','text','file_id','created_dts','is_deleted','deleted_id');
        }else{
            throw $this->exception('This User cannot add comments','API_CannotAdd');
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

        if($r->canAddCommentToTask($u['id'])){
            $fields = array('id','task_id','user_id','text','file_id','created_dts','is_deleted','deleted_id');
        }elseif($u['id'] !=$this['user_id']){
            throw $this->exception('Users are not allowed to edit another\'s comments','API_CannotEdit');
        }else{
            throw $this->exception('This User cannot edit comments','API_CannotEdit');
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

        if($r->canAddCommentToTask($u['id'])){
            return $this;
        }elseif($u['id'] !=$this['user_id']){
            throw $this->exception('Users are not allowed to delete another\'s comments','API_CannotDelete');
        }else{
            throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
        }
    }
}
