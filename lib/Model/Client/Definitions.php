<?php
class Model_Client_Definitions extends Model_BaseTable {
    public $table='client';

    function init(){
        parent::init();

        $this->newField('name');

        $this->addField('email');
        $this->addField('is_archive')->type('boolean');

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');
        $this->addField('deleted_id')->refModel('Model_User');

        $this->addField('organisation_id')->refModel('Model_Organisation');

        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });
    }
}
