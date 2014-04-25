<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 3:01 PM
 */
class Form_Filter_WithDate extends Form_Filter_Base {
	protected $date_from = false;
	protected $date_to   = false;
    function init() {
        parent::init();

	    $this->addDateFrom();
	    $this->addDateTo();
    }
	function addDateFrom(){
		$this->date_from = $this->addField('DatePicker','date_from');

		// set value
		if ($g = $_GET['date_from']) {
			$this->date_from->set($g);
		}
		// reload on change
		$this->date_from->selectnemu_options = array(
			'change' => $this->js(null,'
                function() {'.
					$this->js()->colubris()->reloadForm($this->name,'date_from')
					.'}'
				)
		);
	}
	function addDateTo(){
		$this->date_to = $this->addField('DatePicker','date_to');

		// set value
		if ($g = $_GET['date_to']) {
			$this->date_to->set($g);
		}
		// reload on change
		$this->date_to->select_options = array(
			'change' => $this->js(null,'
                function() {'.
					$this->js()->colubris()->reloadForm($this->name,'date_to')
					.'}'
				)
		);
	}
}