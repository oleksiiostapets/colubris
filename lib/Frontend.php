<?php
class Frontend extends ApiFrontend {
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

        $this->js(true)->_load('colubris');

        // controllers
        $this->colubris = $this->add('Controller_Colubris');
        $this->formatter=$this->add('Controller_Formatter');
        $this->mailer=$this->add('Controller_Mailer');
        
        if($this->page=='logout'){
        	setcookie("colubris_auth_useremail", "", time()-3600);
        }

        // auth
        $this->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User_All', 'email', 'password')
        ;
        $this->api->auth->add('auth/Controller_Cookie');

        if(!$this->api->auth->model['id']){
        	if($_COOKIE["colubris_auth_useremail"] != NULL){
        		$this->api->auth->login($_COOKIE["colubris_auth_useremail"]);
        	}
        }


        $this->template->set('page_title','Colubris');
        
        $this->autoLogin();

        $this->defineAllowedPages();

        $this->task_statuses = array(
                'unstarted'=>'unstarted',
                'started'=>'started',
            	'finished'=>'finished',
                'tested'=>'tested',
            	'rejected'=>'rejected',
            	'accepted'=>'accepted',
            );
        $this->task_types = array(
            'project'=>'project',
            'change request'=>'change request',
            'bug'=>'bug',
            'support'=>'support',
            'drop'=>'drop',
        );
    }

    function initLayout(){

        $this->add('MyMenu', 'Menu', 'Menu');
        $this->add('MySubMenu', 'SubMenu', 'SubMenu');

        // show current user name
        $this->add('View', null, 'name')
            ->setStyle('width', '700px')->setStyle('text-align', 'right')
            ->add('Text')->set($this->api->auth->model['name'] . ' @ ' .'Colubris Team Manager');

        $this->template->trySet('year',date('Y',time()));

        try {
            parent::initLayout();
        } catch (Exception_Denied $e) {
            // TODO show denied page
            throw $e;
        }

    }
    
    function getUserType(){
    	if ($this->currentUser()->isManager()) return 'manager';
    	if ($this->currentUser()->isDeveloper()) return 'team';
    	if ($this->currentUser()->isClient()) return 'client';
    	if ($this->currentUser()->isAdmin()) return 'admin';
    	//if ($this->auth->model['is_system']) return 'system';
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
            $this->api->auth->addInfo('user',$u);
            $this->api->auth->login($u['email']);
        }
    }

    private function defineAllowedPages() {
        // Allowed pages for guest
        $this->addAllowedPages(array(
            'index', 'intro', 'denied',
        ));

        // For Guests
        if (!$this->auth->isLoggedIn())
        if(!$this->auth->isPageAllowed($this->page)){
            $this->api->redirect('index');
        }

        if (!$this->currentUser()->isSystem()) {
            // Access for all non-system roles
            $this->addAllowedPages(array(
                'account', 'about', 'home', 'quotes','clients','projects','tasks','reports','deleted'
            ));

            // Access for managers
            if($this->currentUser()->isManager()) {
                $this->addAllowedPages(array(
                    'manager'
                ));
            }
            // Access for developers
            if($this->currentUser()->isDeveloper()) {
                $this->addAllowedPages(array(
                    'team',
                ));
            }
            // Access for clients
            if($this->currentUser()->isClient()) {
            	$this->addAllowedPages(array(
                    'client',
            	));
            }
            // Access for admin
            if($this->currentUser()->isAdmin()) {
            	$this->addAllowedPages(array(
                    'client', 'manager', 'team', 'admin',
            	));
            }
        }

        if(!$this->api->auth->isPageAllowed($this->page)){
            throw $this->exception('This user cannot see this page','Exception_Denied');
        }
    }
    private $allowed_pages=array();
    private function addAllowedPages($allowed_pages){
        $this->allowed_pages = array_merge($allowed_pages,$this->allowed_pages);

        // allow all subpages of allowed pages
        $page = explode("_",$this->page);
        if( $page[1] && in_array($page[0],$this->allowed_pages) ) $this->allowed_pages[]=$this->page;
        $this->allowed_pages = array_unique($this->allowed_pages);

        $this->auth->allowPage($this->allowed_pages);
    }

    function currentUser() {
        return $this->auth->model;
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
}
