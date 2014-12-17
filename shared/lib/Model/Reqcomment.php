<?php
class Model_Reqcomment extends Model_Auditable {
	public $table='reqcomment';
	function init(){
		parent::init();//$this->debug();
		$this->hasOne('Requirement');
		$this->hasOne('User')->Caption('Creator');
		$this->addField('text')->type('text')->mandatory('required');

		$attach = $this->add('filestore/Field_Image','file_id')->setModel('Model_Myfile');
		$attach->addThumb();

		$this->addField('created_dts')->Caption('Created At')->sortable(true);

		$this->addField('is_deleted')->type('boolean')->defaultValue('0');

		$this->hasOne('User','deleted_id');

        $this->addExpression('user_avatar_thumb')->set(function($m,$q){
            return $q->dsql()
                ->table('user')
                ->table('filestore_file')
                ->table('filestore_image')
                ->field('filename')
                ->where('user.id',$q->getField('user_id'))
                ->where('filestore_image.original_file_id=user.avatar_id')
                ->where('filestore_image.thumb_file_id=filestore_file.id')
                ;
        });

		$this->addHooks();
	}

	// ------------------------------------------------------------------------------
	//
	//            HOOKS :: BEGIN
	//
	// ------------------------------------------------------------------------------

	function addHooks() {
		$this->addHook('beforeDelete', function($m){
			$m['deleted_id']=$m->app->currentUser()->get('id');
		});

		$this->addHook('beforeInsert',function($m,$q){
			$q->set('user_id',$q->app->currentUser()->get('id'));
			$q->set('created_dts', $q->expr('now()'));
		});

//		$this->addHook('beforeSave',function($m){
//			if($m['user_id']>0){
//				if($m->api->auth->model['id']!=$m['user_id']){
//					throw $m
//						->exception('You have no permissions to do this','ValidityCheck')
//						->setField('text');
//				}
//			}
//		});
//		$this->addHook('beforeDelete',function($m){
//			if($m['user_id']>0){
//				if($m->api->auth->model['id']!=$m['user_id']){
//					throw $m
//						->exception('You have no permissions to do this','ValidityCheck');
//				}
//			}
//		});
	}

	function deleted() {
		//$this->addCondition('organisation_id',$this->app->currentUser()->get('organisation_id'));
		$this->addCondition('is_deleted',true);
		return $this;
	}
	function notDeleted() {
		$this->addCondition('is_deleted',false);
		return $this;
	}

    // API methods
    function prepareForSelect(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddCommentToRequirement($u['id'])){
            $fields = array('id','requirement_id','user_id','user','text','file_id','created_dts','is_deleted','deleted_id',
                'user_avatar_thumb');
        }else{
            throw $this->exception('This User cannot see comments','API_CannotSee');
        }

        $this->setActualFields($fields);
        return $this;
    }
    function prepareForInsert(Model_User $u){
        $r = $this->add('Model_User_Right');

        $fields = ['id'];

        if($r->canAddCommentToRequirement($u['id'])){
            $fields = array('id','requirement_id','user_id','text','file_id','created_dts','is_deleted','deleted_id',
                'user_avatar_thumb');
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

        if($r->canAddCommentToRequirement($u['id'])){
            $fields = array('id','requirement_id','user_id','text','file_id','created_dts','is_deleted','deleted_id',
                'user_avatar_thumb');
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

        if($r->canAddCommentToRequirement($u['id'])){
            return $this;
        }elseif($u['id'] !=$this['user_id']){
            throw $this->exception('Users are not allowed to delete another\'s comments','API_CannotDelete');
        }else{
            throw $this->exception('This user has no permissions for deleting','API_CannotDelete');
        }
    }
}
