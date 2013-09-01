<?php
class MySubMenu extends Menu_Basic {
    function init() {
        parent::init();

        // add submenu items
        $p = explode('_', $this->api->page);
        $sub_menu_role = $p[0];
        switch ($sub_menu_role) {
            case 'client':
                $this->addMenuItem('client/dashboard','Dashboard');
            	$this->addMenuItem('client/tasks','Tasks');
            	$this->addMenuItem('client/projects','Projects');
                //$this->addMenuItem('quotes','Quotes');
                $this->addMenuItem('client/reports','Reports');
                //$this->addMenuItem('client/quotes/rfq','Request For Quotation');
                //$m->addMenuItem('client/budgets','Budgets');
                //$m->addMenuItem('client/status','Project Status');
                //if($this->api->auth->model['is_timereport']){
                //$m->addMenuItem('client/timesheets','Time Reports');
                //}
                break;
            case 'team':
                $this->addMenuItem('team/dashboard','Dashboard');
            	$this->addMenuItem('team/tasks','Tasks');
            	$this->addMenuItem('team/projects','Projects');
                //$this->addMenuItem('quotes','Quotes');
                $this->addMenuItem('team/reports','Reports');
                $this->addMenuItem('team/deleted','Deleted');
                //$m->addMenuItem('team/entry','Time Entry');
                //$m->addMenuItem('team/timesheets','Development Priorities');
                //$m->addMenuItem('team/timesheets','Timesheets');
                //$m->addMenuItem('team/budgets','Budgets');
                break;
            case 'manager':
                $this->addMenuItem('manager/dashboard','Dashboard');
            	$this->addMenuItem('manager/tasks','Tasks');
            	$this->addMenuItem('manager/projects','Projects');
                //$this->addMenuItem('quotes','Quotes');
                //$this->addMenuItem('manager/clients','Clients');
                $this->addMenuItem('manager/reports','Reports');
                $this->addMenuItem('manager/deleted','Deleted');
                //$this->addMenuItem('manager/projects','Projects'); // Admin can setup projects and users here
                //$m->addMenuItem('manager/statistics','Statistics');
                //$m->addMenuItem('manager/timesheets','Timesheets'); // review all reports in system - temporary

                //$m->addMenuItem('manager/tasks','Tasks'); // review all tasks in system - temporary
                //$m->addMenuItem('manager/req','Requirements'); // PM can define project requirements here and view tasks
                //$m->addMenuItem('manager/budgets','Budgets'); // Admin can setup projects and users here
                break;
            case 'admin':
                $this->addMenuItem('admin/users','Users');
                $this->addMenuItem('admin/developers','Developers');
                //$m->addMenuItem('manager/clients','Clients');
                //$m->addMenuItem('admin/filestore','Files');
                break;
            case 'system':
                $this->addMenuItem('system/organisation','Organisation');
                //$this->addMenuItem('system/admins','Admins');
                $this->addMenuItem('system/users','Users');
                //$this->addMenuItem('system/developers','Developers');
                $this->addMenuItem('system/system','System Admins');
                break;
            default:
        }
    }


	function isCurrent($href){
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);
		
		if ($href==substr($this->api->page,0,strlen($href))) { return true; }
		
		return $href==$this->api->page||$href==';'.$this->api->page||$href.$this->api->getConfig('url_postfix','')==$this->api->page;
	}
}