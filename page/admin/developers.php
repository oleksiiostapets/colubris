<?php
class page_admin_developers extends Page {
	function init(){
		parent::init();
		$m=$this->add('Model_Developer');
		$m->getField('name')->readonly(true);
		$m->getField('email')->readonly(true);
		$m->getField('weekly_target')->readonly(false);

		$this->add('CRUD')->setModel($m,array('name','email','weekly_target'));

	}
}
