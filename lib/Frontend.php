<?php
class Frontend extends ApiFrontend {
    private $allowed_pages=array();
    
    function init() {
        parent::init();

        /* ************************
         *   PATHFINDER
         */
        $this->pathfinder->addLocation('./',array(
            'addons'=>array('../atk4-addons','../addons'),
            'php'=>array('../shared'),
            'css'=>array(
                '../addons/cms/templates/default/css',
            ),
//            'js'=>array(
//                '../addons/cms/templates/js',
//            ),
            //'template'=>'atk4-addons/misc/templates',
        ));
        
        $this->dbConnect();
        $this->add('jUI');
        
        $this->formatter=$this->add('Controller_Formatter');
        
        $this->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User', 'email', 'password')
        ;

        // Autologin from admin/users
        if( (isset($_REQUEST['id'])) && (isset($_REQUEST['hash'])) ){
            $u=$this->add('Model_User')->load($_GET["id"]);
            if($u['hash']!=$_GET["hash"]){
                echo json_encode("Wrong user hash");
                $this->logVar('wrong user hash: '.$v['hash']);
                exit;
            }

            unset($u['password']);
            $this->api->auth->addInfo($u);
            $this->api->auth->login($u['email']);
        }

        // Allowed pages for guest
        $this->addAllowedPages(array(
            'index',
            'intro',
            'denied',
            ));
        
        // For Guests
        if (!$this->auth->isLoggedIn()){
            if(!$this->auth->isPageAllowed($this->page)){
                $this->api->redirect('index');
            }
        // Admins have access for everything
        }elseif(!$this->api->auth->model['is_admin']) {
            $this->addAllowedPages(array(
                'account',
                'about',
                'home',
                ));

            // Access for managers
            if($this->api->auth->model['is_manager']) {
                $this->addAllowedPages(array(
                    'manager',
                ));
            }
            // Access for developers
            if($this->api->auth->model['is_developer']) {
                $this->addAllowedPages(array(
                    'team',
                    ));
            }
            // Access for clients
            if($this->api->auth->model['is_client']) {
            	$this->addAllowedPages(array(
            			'client',
            	));
            }
            
            if(!$this->api->auth->isPageAllowed($this->page)){
                $this->api->redirect('denied');
            }
        }
        $this->task_statuses=array(
                'unstarted'=>'unstarted',
                'started'=>'started',
            	'finished'=>'finished',
            	'rejected'=>'rejected',
            	'accepted'=>'accepted',
            );
    }
    
    function addAllowedPages($allowed_pages){
        $this->allowed_pages=array_merge($allowed_pages,$this->allowed_pages);

        $page=explode("_",$this->page);
        if( ($page[1]) && (in_array($page[0],$this->allowed_pages)) ) $this->allowed_pages[]=$this->page;
        $this->allowed_pages=array_unique($this->allowed_pages);

        $this->auth->allowPage($this->allowed_pages);
    }
    
    function initLayout(){

        $m = $this->add('Mymenu', 'Menu', 'Menu');
        $sm = $this->add('Mysubmenu', 'SubMenu', 'SubMenu');

        if(!$this->auth->isLoggedIn()){
        	//break;
        }
        
        if ($this->api->auth->model['is_manager'] || $this->api->auth->model['is_admin']) {
        	$m->addMenuItem('manager/tasks','Manager');
        }
        if ($this->api->auth->model['is_developer']) {
        	$m->addMenuItem('team/tasks','Developer');
        }
        if ($this->api->auth->model['is_client']) {
        	$m->addMenuItem('client/tasks','Client');
        }
        if ($this->api->auth->model['is_admin']) {
        	$m->addMenuItem('admin/users','Admin');
        }

        if($this->auth->isLoggedIn()){
        	$m->addMenuItem('account','Settings');
        }
        $m->addMenuItem('about','About');
        
        $p = explode('_', $this->page);
        switch ($p[0]) {
            case 'client':
                $sm->addMenuItem('client/tasks','Tasks');
            	$sm->addMenuItem('client/projects','Projects');
                $sm->addMenuItem('client/quotes','Quotes');
                //$sm->addMenuItem('client/quotes/rfq','Request For Quotation');
                //$m->addMenuItem('client/budgets','Budgets');
                //$m->addMenuItem('client/status','Project Status');
                //if($this->api->auth->model['is_timereport']){
                //$m->addMenuItem('client/timesheets','Time Reports');
                //}
                break;

            case 'team':
                $sm->addMenuItem('team/tasks','Tasks');
            	$sm->addMenuItem('team/quotes','Quotes');
                //$m->addMenuItem('team/entry','Time Entry');
                //$m->addMenuItem('team/timesheets','Development Priorities');
                //$m->addMenuItem('team/timesheets','Timesheets');
                //$m->addMenuItem('team/budgets','Budgets');
                break;

            case 'manager':
                $sm->addMenuItem('manager/tasks','Tasks');
            	$sm->addMenuItem('manager/projects','Projects');
                $sm->addMenuItem('manager/quotes','Quotes');
                $sm->addMenuItem('manager/clients','Clients');
                //$sm->addMenuItem('manager/projects','Projects'); // Admin can setup projects and users here
                //$m->addMenuItem('manager/statistics','Statistics');
                //$m->addMenuItem('manager/reports','Reports'); // review all reports in system - temporary
                //$m->addMenuItem('manager/timesheets','Timesheets'); // review all reports in system - temporary

                //$m->addMenuItem('manager/tasks','Tasks'); // review all tasks in system - temporary
                //$m->addMenuItem('manager/req','Requirements'); // PM can define project requirements here and view tasks
                //$m->addMenuItem('manager/budgets','Budgets'); // Admin can setup projects and users here
                break;
            case 'admin':
                $sm->addMenuItem('admin/users','Users');
                $sm->addMenuItem('admin/developers','Developers');
                //$m->addMenuItem('manager/clients','Clients');
                //$m->addMenuItem('admin/filestore','Files');
                break;

            default:

                break;
        }
        
        if ($this->auth->isLoggedIn() && !$this->api->auth->model['is_client']) {
            //$m->addMenuItem('index','Main Menu');
        }
        if ($this->auth->isLoggedIn()){
            $m->addMenuItem('logout');
        }else{
            $m->addMenuItem('/','Login');
        }
        
        $sc = $this->add('HtmlElement', null, 'name')
                        ->setElement('div')
                        ->setStyle('width', '700px')
                        ->setStyle('text-align', 'right');
        
        $sc->add('Text')
                ->set(
                        $this->api->auth->model['name'] . ' @ ' .
                        'Colubris Team Manager v' . $this->getVersion());

        $this->template->trySet('year',date('Y',time()));
        
        parent::initLayout();
    }

    function getVersion() {
        return '0.2.1';
    }

}
