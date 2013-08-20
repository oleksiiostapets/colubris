<?php
class Model_Project_Base extends Model_BaseTable {
    public $table='project';

    function init(){
        parent::init();

        $this->addField('name')->mandatory('required');
        $this->addField('descr')->dataType('text');

        $this->addField('client_id')->refModel('Model_Client');

        $this->addField('demo_url');
        $this->addField('prod_url');

        $this->addField('organisation_id')->refModel('Model_Organisation');
        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');

        if($this->api->auth->model['is_client']){
            $this->addCondition('client_id',$this->api->auth->model['client_id']);
        }

        $this->setOrder('name');
    }

}
