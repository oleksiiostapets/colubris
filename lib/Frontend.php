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
        
        $this->mailer=$this->add('Controller_Mailer');
        
        if($this->page=='logout'){
        	setcookie("colubris_auth_useremail", "", time()-3600);
        }
        
        $this->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User_All', 'email', 'password')
        ;
        $this->api->auth->add('auth/Controller_Cookie');
        $this->add('x_breakpoint/Controller_Breakpoint');

        if(!$this->api->auth->model['id']){
        	if($_COOKIE["colubris_auth_useremail"] != NULL){
        		$this->api->auth->login($_COOKIE["colubris_auth_useremail"]);
        	}
        }
        
        $this->template->set('page_title','Colubris');
        
        $this->autoLogin();

        $this->defineAllowedPages();

        $this->task_statuses=array(
                'unstarted'=>'unstarted',
                'started'=>'started',
            	'finished'=>'finished',
                'tested'=>'tested',
            	'rejected'=>'rejected',
            	'accepted'=>'accepted',
            );
        $this->task_types=array(
            'project'=>'project',
            'change request'=>'change request',
            'bug'=>'bug',
            'support'=>'support',
            'drop'=>'drop',
        );
    }

    function initLayout(){

        $m = $this->add('Mymenu', 'Menu', 'Menu');
        $sm = $this->add('Mysubmenu', 'SubMenu', 'SubMenu');

        if(!$this->auth->isLoggedIn()){
        	//break;
        }
        
        if ($this->api->auth->model['is_manager'] || $this->api->auth->model['is_admin']) {
        	$m->addMenuItem('manager/dashboard','Manager');
        }
        if ($this->api->auth->model['is_developer']) {
        	$m->addMenuItem('team/dashboard','Developer');
        }
        if ($this->api->auth->model['is_client']) {
        	$m->addMenuItem('client/dashboard','Client');
        }
        if ($this->api->auth->model['is_admin']) {
        	$m->addMenuItem('admin/users','Admin');
        }
        if ($this->api->auth->model['is_system']) {
        	$m->addMenuItem('system/organisation','System');
        }

        if($this->auth->isLoggedIn() && !$this->api->auth->model['is_system']){
        	$m->addMenuItem('account','Settings');
        }
        $m->addMenuItem('about','About');
        
        $p = explode('_', $this->page);
        switch ($p[0]) {
            case 'client':
                $sm->addMenuItem('client/dashboard','Dashboard');
            	$sm->addMenuItem('client/tasks','Tasks');
            	$sm->addMenuItem('client/projects','Projects');
                $sm->addMenuItem('client/quotes','Quotes');
                $sm->addMenuItem('client/reports','Reports');
                //$sm->addMenuItem('client/quotes/rfq','Request For Quotation');
                //$m->addMenuItem('client/budgets','Budgets');
                //$m->addMenuItem('client/status','Project Status');
                //if($this->api->auth->model['is_timereport']){
                //$m->addMenuItem('client/timesheets','Time Reports');
                //}
                break;
            case 'team':
                $sm->addMenuItem('team/dashboard','Dashboard');
            	$sm->addMenuItem('team/tasks','Tasks');
            	$sm->addMenuItem('team/projects','Projects');
                $sm->addMenuItem('team/quotes','Quotes');
                $sm->addMenuItem('team/reports','Reports');
                $sm->addMenuItem('team/deleted','Deleted');
                //$m->addMenuItem('team/entry','Time Entry');
                //$m->addMenuItem('team/timesheets','Development Priorities');
                //$m->addMenuItem('team/timesheets','Timesheets');
                //$m->addMenuItem('team/budgets','Budgets');
                break;
            case 'manager':
                $sm->addMenuItem('manager/dashboard','Dashboard');
            	$sm->addMenuItem('manager/tasks','Tasks');
            	$sm->addMenuItem('manager/projects','Projects');
                $sm->addMenuItem('manager/quotes','Quotes');
                $sm->addMenuItem('manager/clients','Clients');
                $sm->addMenuItem('manager/reports','Reports');
                $sm->addMenuItem('manager/deleted','Deleted');
                //$sm->addMenuItem('manager/projects','Projects'); // Admin can setup projects and users here
                //$m->addMenuItem('manager/statistics','Statistics');
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
            case 'system':
                $sm->addMenuItem('system/organisation','Organisation');
                //$sm->addMenuItem('system/admins','Admins');
                $sm->addMenuItem('system/users','Users');
                //$sm->addMenuItem('system/developers','Developers');
                $sm->addMenuItem('system/system','System Admins');
                break;
            case 'index':
            case 'home':
            case 'account':
            case 'about':
            case 'trace':
                break;
            default:
                throw $this->exception('There is no shuch a role '.$p[0]);
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
                        'Colubris Team Manager');

        $this->template->trySet('year',date('Y',time()));
        
        parent::initLayout();
    }

    function makeUrls($text) {
        preg_match_all(
            '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/',
            $text,$matches
        );

        $replaced = array();
        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                if (in_array($match,$replaced)) continue;
                $text = str_replace($match,'<a href="'.$match.'" target="_blank">'.$match.'</a>',$text);
                $replaced[] = $match;
            }
        }
        return $text;
    }

    /* ************************
     *      TRANSLATIONS
    */
    private $translations = false;
    function _($string) {
    	// add translation if not exist yet
    	if (!is_object($this->translations)) $this->translations = $this->add('Controller_Translator');
    
    	// do not translate if only spases
    	if(!is_array($string)){
    		if (trim($string) == '') return $string;
    	}
    
    	// check if passed twise throw translation, can be comented on production
    	if(strpos($string,"\xe2\x80\x8b")!==false){
    		throw new BaseException('String '.$string.' passed through _() twice');
    	}
    	return $this->translations->__($string)."\xe2\x80\x8b";
    
    	//return $this->translations->__($string);
    }
    
    function getUserType(){
    	if ($this->auth->model['is_manager']) return 'manager';
    	if ($this->auth->model['is_developer']) return 'team';
    	if ($this->auth->model['is_client']) return 'client';
    	if ($this->auth->model['is_admin']) return 'admin';
    	if ($this->auth->model['is_system']) return 'system';
    }

    function siteURL(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
    }

    function autoLogin() {
        // Autologin from admin/users
        if( (isset($_REQUEST['id'])) && (isset($_REQUEST['hash'])) ){
            $u=$this->add('Model_User')->load($_GET["id"]);
            if($u['hash']!=$_GET["hash"]){
                echo json_encode("Wrong user hash");
                $this->logVar('wrong user hash: '.$u['hash']);
                exit;
            }
            unset($u['password']);
            $this->api->auth->addInfo($u);
            $this->api->auth->login($u['email']);
        }
    }

    private function defineAllowedPages() {
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
        // System has access for everything
        } elseif (!$this->api->auth->model['is_system']) {
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
            // Access for admin
            if($this->api->auth->model['is_admin']) {
            	$this->addAllowedPages(array(
                    'client',
                    'manager',
                    'team',
                    'admin',
            	));
            }

            if(!$this->api->auth->isPageAllowed($this->page)){
                $this->api->redirect('denied');
            }
        }
    }
    private function addAllowedPages($allowed_pages){
        $this->allowed_pages=array_merge($allowed_pages,$this->allowed_pages);

        $page=explode("_",$this->page);
        if( ($page[1]) && (in_array($page[0],$this->allowed_pages)) ) $this->allowed_pages[]=$this->page;
        $this->allowed_pages=array_unique($this->allowed_pages);

        $this->auth->allowPage($this->allowed_pages);
    }
}
