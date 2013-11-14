<?php
class MyMenu extends Menu_Basic {
    function init() {
        parent::init();
        if($this->api->auth->isLoggedIn()) {
            if ($this->api->currentUser()->canSeeDashboard()) $this->addMenuItem('dashboard','Dashboard');
            if ($this->api->currentUser()->canSeeTaskList()) $this->addMenuItem('tasks','Tasks');
            if ($this->api->currentUser()->canSeeQuotesList()) $this->addMenuItem('quotes','Quotes');
            if ($this->api->currentUser()->canUserMenageClients()) $this->addMenuItem('clients','Clients');
            if ($this->api->currentUser()->canSeeProjectList()) $this->addMenuItem('projects','Projects');
            if ($this->api->currentUser()->canSeeReportList()) $this->addMenuItem('reports','Reports');
            if ($this->api->currentUser()->canSeeUserList()) $this->addMenuItem('users','Users');
            if ($this->api->currentUser()->canSeeDevList()) $this->addMenuItem('developers','Developers');
            if ($this->api->currentUser()->canSeeDeleted()) $this->addMenuItem('deleted','Deleted');
            if ($this->api->currentUser()->canSeeLogs()) $this->addMenuItem('logs','Logs');
            if ($this->api->currentUser()->canSeeFinance()) $this->addMenuItem('rates','Rates');
        }




        if ($this->api->currentUser()->isSystem()) {
            $this->addMenuItem('system/users','Users');
            $this->addMenuItem('system/system','System');
            $this->addMenuItem('system/organisation','Organisation');
	}

        if($this->api->auth->isLoggedIn() && !$this->api->currentUser()->canBeSystem()) {
        	$this->addMenuItem('account','Settings');
        }
        $this->addMenuItem('about','About');
        if ($this->api->auth->isLoggedIn()){
            $this->addMenuItem('logout');
        }else{
            $this->addMenuItem('/','Login');
        }
        //if(!$this->api->currentUser()->canBeAdmin()){
            if($_COOKIE['fuser']){
                $u=$this->add('Model_User')->load($_COOKIE['fuser']);
                $this->api->template->trySetHtml('link2first_user','<br /><a href="?id='.$_COOKIE['fuser'].'&hash='.$_COOKIE['fhash'].'&clear=1">Back as '.$u['name'].'</a>');
            }
        //}
    }
	function isCurrent($href){
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);

		if ( (substr($href,0,7)=='manager' && substr($this->api->page,0,7)=='manager') ) { return true; }
		if ( (substr($href,0,5)=='admin' && substr($this->api->page,0,5)=='admin') ) { return true; }
		if ( (substr($href,0,4)=='team' && substr($this->api->page,0,4)=='team') ) { return true; }
		if ( (substr($href,0,6)=='client' && substr($this->api->page,0,6)=='client') ) { return true; }
		if ( (substr($href,0,13)=='system/system' && substr($this->api->page,0,13)=='system/system') ) { return true; }
		if ( (substr($href,0,18)=='system/organisation' && substr($this->api->page,0,18)=='system/organisation') ) { return true; }



        if ( (substr($href,0,6)=='quotes' && substr($this->api->page,0,6)=='quotes') ) { return true; }
        if ( (substr($href,0,7)=='clients' && substr($this->api->page,0,7)=='clients') ) { return true; }
        if ( (substr($href,0,5)=='users' && substr($this->api->page,0,5)=='users') ) { return true; }
        if ( (substr($href,0,10)=='developers' && substr($this->api->page,0,10)=='developers') ) { return true; }

        return $href==$this->api->page||$href==';'.$this->api->page||$href.$this->api->getConfig('url_postfix','')==$this->api->page;
	}
}
