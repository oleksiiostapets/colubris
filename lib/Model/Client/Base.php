<?php
class Model_Client_Base extends Model_BaseTable {
    public $table='client';

    function init(){
        parent::init();

        $this->newField('name');

        //$this->addField('smbo_id')->type('int');
        $this->addField('email');
        $this->addField('is_archive')->type('boolean');

        //$this->addField('total_sales')->type('money');
        //$this->addField('ebalance')->type('int');
        //$this->addField('day_credit')->type('int');

        $this->addField('is_deleted')->type('boolean')->defaultValue('0');

        $this->addField('organisation_id')->refModel('Model_Organisation');

        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);
    }
}
