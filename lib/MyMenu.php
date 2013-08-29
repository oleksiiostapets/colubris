<?php
class MyMenu extends Menu_Basic {
    function init() {
        parent::init();

        if ($this->api->currentUser()->isManager()) {
        	$this->addMenuItem('manager/dashboard','Manager');
        }
        if ($this->api->currentUser()->isDeveloper()) {
        	$this->addMenuItem('team/dashboard','Developer');
        }
        if ($this->api->currentUser()->isClient()) {
        	$this->addMenuItem('client/dashboard','Client');
        }
        if ($this->api->currentUser()->isAdmin()) {
        	$this->addMenuItem('admin/users','Admin');
        }
        if ($this->api->currentUser()->isSystem()) {
        	$this->addMenuItem('system/organisation','System');
        }

        if($this->api->auth->isLoggedIn() && !$this->api->currentUser()->isSystem()) {
        	$this->addMenuItem('account','Settings');
        }
        $this->addMenuItem('about','About');

        if ($this->api->auth->isLoggedIn() && !$this->api->currentUser()->isClient()) {
            //$this->addMenuItem('index','Main Menu');
        }
        if ($this->api->auth->isLoggedIn()){
            $this->addMenuItem('logout');
        }else{
            $this->addMenuItem('/','Login');
        }
    }
	function isCurrent($href){
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);

		if ( (substr($href,0,7)=='manager' && substr($this->api->page,0,7)=='manager') ) { return true; }
		if ( (substr($href,0,5)=='admin' && substr($this->api->page,0,5)=='admin') ) { return true; }
		if ( (substr($href,0,4)=='team' && substr($this->api->page,0,4)=='team') ) { return true; }
		if ( (substr($href,0,6)=='client' && substr($this->api->page,0,6)=='client') ) { return true; }
		if ( (substr($href,0,6)=='system' && substr($this->api->page,0,6)=='system') ) { return true; }

		return $href==$this->api->page||$href==';'.$this->api->page||$href.$this->api->getConfig('url_postfix','')==$this->api->page;
	}
}