<?php
class Model_User_Base extends Model_BaseTable {
    public $table='user';
    function init(){
        parent::init(); //$this->debug();
        if (@$this->api->auth) $this->api->auth->addEncryptionHook($this);

        // fields
        $this->addField('email')->mandatory('required');
        $this->addField('name');
        $this->addField('password')->display(array('form'=>'password'))->mandatory('required');
        $this->addField('client_id')->refModel('Model_Client');
        $this->addField('is_admin')->type('boolean');
        $this->addField('is_manager')->type('boolean');
        $this->addField('is_developer')->type('boolean');
        $this->addField('is_timereport')->type('boolean')->caption('Is Time Reports');
        $this->addField('hash');
        $this->addField('mail_task_changes')->type('boolean')->caption('Send when task changed');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('is_system')->defaultValue('0')->type('boolean');

        $this->hasOne('Organisation')->mandatory('required');

        // expressions
        $this->addExpression('is_client')->datatype('boolean')->set(function($m,$q){
            return $q->dsql()
                    ->expr('if(client_id is null,false,true)');
        });

        // order
        $this->setOrder('name');


        // hooks
        $this->addHook('beforeInsert',function($m){
            if($m->getBy('email',$m['email'])) throw $m
                    ->exception('User with this email already exists','ValidityCheck')
                    ->setField('email');
        });

        $this->addHook('beforeModify',function($m){
            if($m->dirty['email']) throw $m
                ->exception('Do not change email for existing user','ValidityCheck')
                ->setField('email');
        });
    }
    function me(){
        $this->addCondition('id',$this->api->auth->get('id'));
        return $this;
    }
    function beforeInsert(&$d){
        $d['hash']=md5(uniqid());
        return parent::beforeInsert($d);
    }
    function resetPassword(){
        throw $this->exception('Function resetPassword is not implemented yet');
    }
}
