<?php
class MyMenu extends Menu_Basic {
    function init() {
        parent::init();
        if($this->app->currentUser()) {
            if ($this->app->model_user_rights->canSeeDashboard()) $this->addMenuItem('dashboard','Dashboard');
            if ($this->app->model_user_rights->canSeeTasks()) $this->addMenuItem('tasks','Tasks');
            if ($this->app->model_user_rights->canSeeQuotes()) $this->addMenuItem('quotes','Quotes');
            if ($this->app->model_user_rights->canSeeProjects()) $this->addMenuItem('projects','Projects');
            if ($this->app->model_user_rights->canManageClients()) $this->addMenuItem('clients','Clients');
            if ($this->app->model_user_rights->canSeeReports()) $this->addMenuItem('reports','Reports');
            if ($this->app->model_user_rights->canSeeDevelopers()) $this->addMenuItem('developers','Developers');
            if ($this->app->model_user_rights->canSeeDeleted()) $this->addMenuItem('deleted','Deleted');
            if ($this->app->model_user_rights->canSeeUsers()) $this->addMenuItem('users','Users');
            if ($this->app->model_user_rights->canSeeLogs()) $this->addMenuItem('logs','Logs');
            if ($this->app->model_user_rights->canSeeRates()) $this->addMenuItem('rates','Rates');
            $this->addMenuItem('content','Content');
            if ($this->app->model_user_rights->canSeeSettings()) $this->addMenuItem('account','Settings');
            if ($this->app->model_user_rights->canSeeManager()) $this->addMenuItem('manager','Manager');
        }

        $this->addMenuItem('about','About');
        if ($this->app->currentUser()){
            $this->addMenuItem('logout');
        }else{
            $this->addMenuItem('/','Login');
        }
        //if(!$this->app->currentUser()->canBeAdmin()){
            if($_COOKIE['fuser']){
                $u=$this->add('Model_User')->getActive();
				$u->load($_COOKIE['fuser']);
                $this->app->template->trySetHtml('link2first_user','<br /><a href="?id='.$_COOKIE['fuser'].'&hash='.$_COOKIE['fhash'].'&clear=1">Back as '.$u['name'].'</a>');
            }
        //}
    }
	/*function isCurrent($href){//GET RID
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);

		if ( (substr($href,0,7)=='manager' && substr($this->app->page,0,7)=='manager') ) { return true; }
		if ( (substr($href,0,5)=='admin' && substr($this->app->page,0,5)=='admin') ) { return true; }
		if ( (substr($href,0,4)=='team' && substr($this->app->page,0,4)=='team') ) { return true; }
		if ( (substr($href,0,6)=='client' && substr($this->app->page,0,6)=='client') ) { return true; }
		if ( (substr($href,0,13)=='system/system' && substr($this->app->page,0,13)=='system/system') ) { return true; }
		if ( (substr($href,0,18)=='system/organisation' && substr($this->app->page,0,18)=='system/organisation') ) { return true; }



        if ( (substr($href,0,6)=='quotes' && substr($this->app->page,0,6)=='quotes') ) { return true; }
        if ( (substr($href,0,7)=='clients' && substr($this->app->page,0,7)=='clients') ) { return true; }
        if ( (substr($href,0,5)=='users' && substr($this->app->page,0,5)=='users') ) { return true; }
        if ( (substr($href,0,10)=='developers' && substr($this->app->page,0,10)=='developers') ) { return true; }

        return $href==$this->app->page||$href==';'.$this->app->page||$href.$this->app->getConfig('url_postfix','')==$this->app->page;
	}*/
}
