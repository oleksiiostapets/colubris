<?php
class Model_Project_Guest extends Model_Table {
    public $table='project';

    function init(){
        parent::init();

        $this->addField('name')->mandatory('required');
        $this->addField('descr')->dataType('text');

        $this->addField('client_id')->refModel('Model_Client');

        $this->addField('demo_url');
        $this->addField('prod_url');
        $this->addField('repository');

        $this->addField('organisation_id')->refModel('Model_Organisation');

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');

        $this->setOrder('name');

        $this->addField('deleted_id')->refModel('Model_User');
        $this->addHook('beforeDelete', function($m){
            $m['deleted_id']=$m->api->currentUser()->get('id');
        });
    }
}
