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
        if ($_GET['project'] == null) $_GET['project'] = 0;
        if ($_GET['quote'] == null) $_GET['quote'] = 0;
        if ($_GET['requirement'] == null) $_GET['requirement'] = 0;
        if ($_GET['assigned'] == null) $_GET['assigned'] = 0;
        if ($_GET['date_to'] == null) $_GET['date_to'] = '';
        $js_arr=array(
            $this->app->url(),
            'project'=>$_GET['project'],
            'quote'=>$_GET['quote'],
            'requirement'=>$_GET['requirement'],
            'assigned'=>$_GET['assigned'],
            'date_from'=>$this->date_from->js()->val(),
            'date_to'=>$_GET['date_to']
        );
        $this->date_from->js('change')->univ()->location($js_arr);
	}
	function addDateTo(){
		$this->date_to = $this->addField('DatePicker','date_to');

		// set value
		if ($g = $_GET['date_to']) {
			$this->date_to->set($g);
		}
		// reload on change
        if ($_GET['project'] == null) $_GET['project'] = 0;
        if ($_GET['quote'] == null) $_GET['quote'] = 0;
        if ($_GET['requirement'] == null) $_GET['requirement'] = 0;
        if ($_GET['assigned'] == null) $_GET['assigned'] = 0;
        if ($_GET['date_from'] == null) $_GET['date_from'] = '';
        $js_arr=array(
            $this->app->url(),
            'project'=>$_GET['project'],
            'quote'=>$_GET['quote'],
            'requirement'=>$_GET['requirement'],
            'assigned'=>$_GET['assigned'],
            'date_from'=>$_GET['date_from'],
            'date_to'=>$this->date_to->js()->val()
        );
        $this->date_to->js('change')->univ()->location($js_arr);
	}
}