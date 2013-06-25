<?php
class Model_User extends Model_Table {
    public $table='user';
    function init(){
        parent::init(); //$this->debug();
        if (@$this->api->auth) $this->api->auth->addEncryptionHook($this);

        $this->addField('email')->mandatory('required');

        $this->addField('name');

        $this->addField('password')->display(array('form'=>'password'))->mandatory('required');

        $this->addField('client_id')
                ->refModel('Model_Client');

        $this->addField('is_admin')->type('boolean');
        $this->addField('is_manager')->type('boolean');
        $this->addField('is_developer')->type('boolean');
        $this->addField('is_timereport')->type('boolean')
                ->caption('Is Time Reports');
        $this->addExpression('is_client')->datatype('boolean')->set(function($m,$q){
            return $q->dsql()
                    ->expr('if(client_id is null,false,true)');
        });
        
        $this->addField('hash');

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

    }
}
