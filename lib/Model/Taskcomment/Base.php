<?php
class Model_Taskcomment_Base extends Model_Auditable {
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

            $task=$m->add('Model_Task')->load($m->get('task_id'));
            $m->api->mailer->task_status=$task['status'];
            $m->api->mailer->addReceiverByUserId($task->get('requester_id'),'mail_task_changes');
            $m->api->mailer->addReceiverByUserId($task->get('assigned_id'),'mail_task_changes');
            $m->api->mailer->sendMail('task_comment_changed',array(
                'link'=>$m->api->siteURL().$m->api->url('/task',array('task_id'=>$m->get('task_id'),'colubris_task_view_view_2_crud_virtualpage_id'=>null,'colubris_task_view_view_2_crud_virtualpage'=>null)),
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
}
