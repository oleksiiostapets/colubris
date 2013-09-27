<?php
class Frontend extends ApiFrontend {

    function getVer(){
        return 2.1;
    }
    function init() {
        parent::init();
        $this->checkCookies();

        if(strtolower($this->api->page)=='logout'){
            setcookie("fuser", "", time()-3600);
            setcookie("fhash", "", time()-3600);
        }

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
        $this->colubris  = $this->add('Controller_Colubris');
        $this->formatter = $this->add('Controller_Formatter');
        $this->mailer    = $this->add('Controller_Mailer');
        
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

        $this->role_menu = $this->add('RoleMenu', 'SubMenu', 'SubMenu');


        $this->template->set('page_title','Colubris');
        
        $this->autoLogin();

        if($this->api->auth->model['id']>0){
            if(!$this->currentUser()->isSystem()){
                $this->template->trySet('organisation','for '.$this->currentUser()->get('organisation'));
            }else{
                $this->template->trySet('organisation','for System user');
            }
        }

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
        //$this->add('MySubMenu', 'SubMenu', 'SubMenu');

        // show current user name
        $this->add('View', null, 'name')
            ->setStyle('width', '700px')->setStyle('text-align', 'right')
            ->add('Text')->set($this->api->auth->model['name']?$this->api->auth->model['name']:'Guest' . ' @ ' .'Colubris Team Manager, ver.'.$this->getVer());

        $this->template->trySet('year',date('Y',time()));

        $this->defineAllowedPages();

        try {
            if(!$this->api->auth->isPageAllowed($this->page)){
                throw $this->exception('This user cannot see this page','Exception_Denied');
            }
            parent::initLayout();
        } catch (Exception_Denied $e) {
            // TODO show denied page
            //throw $e;
            $v = $this->add('View')->addClass('denied');
            $v->add('View')->setElement('h2')->set('You cannot see this page');
            $v->add('View_Error')->set('Try to change role if you have multiple roles for this account');
        }

    }
    
    function getUserType(){
    	if ($this->currentUser()->canBeManager()) return 'manager';
    	if ($this->currentUser()->canBeDeveloper()) return 'team';
    	if ($this->currentUser()->canBeClient()) return 'client';
    	if ($this->currentUser()->canBeAdmin()) return 'admin';
    	//if ($this->auth->model['is_system']) return 'system';
    }

    function getCurrentUserRole(){
        return $this->role_menu->getCurrentUserRole();
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
        if (!$this->auth->isLoggedIn()){
            $this->addAllowedPages(array(
                'quotation','quotation2',
            ));
            if(!$this->auth->isPageAllowed($this->page)){
                $this->api->redirect('index');
            }
        }else{

            if (!$this->currentUser()->canBeSystem()) {
                // Access for all non-system roles
                $this->addAllowedPages(array(
                    'account', 'about', 'home', 'quotes','clients','projects','tasks','deleted','developers','users','dashboard','trace','task'
                ));
                // Grant access for non-client users
                if($this->currentUser()->canSeeReportList()){
                    $this->addAllowedPages(array(
                        'reports'
                    ));
                }
            }else{
                $this->addAllowedPages(array(
                    'home','system','about','dashboard'
                ));
            }
            if( ($this->auth->isLoggedIn()) && ($this->currentUser()->canSeeLogs()) ) {
                $this->addAllowedPages(array(
                    'logs'
                ));
            }
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

    function checkCookies() {
        $v = $this->getVer();

        if ( !$_COOKIE['version'] || ($_COOKIE['version'] != $v) ){
            //setcookie('colubris_auth_useremail',null);
            //setcookie('colubris',null);
            //setcookie('version',$v, 60*60*24*30*12*10,'/');
        }
//        $this->redirect($this->url('/'));
    }
}
