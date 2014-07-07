<?php
class Model_Reqcomment_Base extends Model_Auditable {
    public $table='reqcomment';
    function init(){
        parent::init();
        $this->hasOne('Requirement');
        $this->hasOne('User')->Caption('Creator');
        $this->addField('text')->type('text')->mandatory('required');

        $attach = $this->add('filestore/Field_Image','file_id')->setModel('Model_Myfile');
        $attach->addThumb();

        $this->addField('created_dts')->Caption('Created At')->sortable(true);

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('deleted_id')->refModel('Model_User');
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
