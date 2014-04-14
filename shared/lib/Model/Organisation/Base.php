<?php
class Model_Organisation_Base extends Model_BaseTable {
    public $table='organisation';
    function init(){
        parent::init();
        $this->addField('name');
        $this->addField('desc')->type('text');
        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
//        $this->addField('deleted_id')->refModel('Model_User');
        $this->hasOne('User','deleted_id');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });
    }
}