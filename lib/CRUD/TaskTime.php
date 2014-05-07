<?php
/**
 *Created by Konstantin Kolodnitsky
 * Date: 07.05.14
 * Time: 13:32
 */
class CRUD_TaskTime extends CRUD{
	function init(){
		parent::init();

		$m = $this->add('Model_TaskTime')->addCondition('task_id',$this->task_id);
		if ($this->app->currentUser()->isClient()){
			$this->setModel($m,
				array('spent_time','comment','date'),
				array('user','estimate','comment','date','remove_billing')
			);
		} else {
			$this->setModel($m,
				array('spent_time','comment','date','remove_billing'),
				array('user','spent_time','comment','date','remove_billing')
			);
		}
		if ($this->add_button) {
			$this->add_button->setLabel('Add Time');
		}
		if ($this->grid){
			$this->grid->addTotals(array('spent_time'));
			$this->grid->addClass('zebra bordered');
			$this->grid->addPaginator(20);
		}
		$this->js(true)->closest(".ui-dialog")->on("dialogbeforeclose",
			$this->js(null,'function(event, ui){
                              '.$this->js()->_selector('#'.$this->name)->trigger('reload').'
                          }
                      ')
		);
	}
}