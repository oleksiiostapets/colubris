<?php
class Model_Rate extends Model_Table {
	public $table="rate";
	function init(){
		parent::init();
		
		$this->addField('from')->mandatory(true);
		$this->addField('to')->mandatory(true);
		$this->addField('value')->mandatory(true);

//        $this->addField('organisation_id')->refModel('Model_Organisation');
        $this->hasElement('Organisation','organisation_id');
        $this->addCondition('organisation_id',$this->api->auth->model['organisation_id']);

	}

}