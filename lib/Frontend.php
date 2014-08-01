<?php
class Frontend extends ApiFrontend {

    function getVer(){
        return 2.1;
    }
    function init() {
        parent::init();
        $this->checkCookies();

        if(strtolower($this->page)=='logout'){
            setcookie("fuser", "", time()-3600);
            setcookie("fhash", "", time()-3600);
        }

        /* ************************
         *   PATHFINDER
         */
        $this->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib'),
            'mail'=>array('templates/mail'),
            'css'=>array(
                'addons/cms/templates/default/css',
            ),
//            'js'=>array(
//                '../addons/cms/templates/js',
//            ),
            //'template'=>'atk4-addons/misc/templates',
        ))->setBasePath('.');

		$this->dbConnect();
        $this->add('jUI');
        $this->addRouter();

        $this->app->jquery->addStylesheet('custom');

        $this->js(true)->_load('colubris');

        // controllers
        $this->addControllers();

        if ($this->page=='logout') {
            $this->hg_cookie->forgetLoginHash();
//        	setcookie("colubris_auth_useremail", "", time()-3600);
        }

        // auth
        $this->addAuth();

//        $this->layout = $this->add('Layout_Colubris');
        $this->layout = $this->add('Layout_Fluid')->addClass('atk-swatch-ink');
        $this->template->set('page_title','Colubris');
        $this->layout->template->set('page_title','Colubris');

        $view_header = $this->layout->add('View',null,'Header_Content',array('view/header'));

        $this->layout->rm = $view_header->add('RoleMenu', 'SubMenu','SubMenu');
        $view_header->add('MyMenu',null,'Main_Menu');
        //$this->add('MySubMenu', 'SubMenu', 'SubMenu');

        // show current user name
        $view_header->template->set('name',$this->auth->model['name']?$this->auth->model['name']:'Guest' . ' @ ' .'Colubris Team Manager, ver.'.$this->getVer());

        $this->template->trySet('year',date('Y',time()));

        $this->defineAllowedPages();

        $this->layout->addFooter()//->addClass('atk-swatch-ink')
            ->setHTML('
            <div class="row atk-wrapper">
                <div class="col span_8">
                    © 1998 - 2014 Agile55 Limited
                </div>
                <div class="col span_4 atk-align-center">
                    <img src="'.$this->pm->base_path.'images/powered_by_agile.png" alt="powered_by_agile">
                </div>
            </div>
        ');
        
        $this->autoLogin();
    }
    protected function addControllers() {
        $this->colubris    = $this->add('Controller_Colubris');
        $this->formatter   = $this->add('Controller_Formatter');
        $this->mailer      = $this->add('Controller_Mailer');
        $this->hg_cookie   = $this->add('Controller_MyCookie');
        $this->user_access = $this->add('Controller_UserAccess');
    }
    protected function addAuth() {
		$mu=$this->add('Model_User')->notDeleted();
        $this->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel($mu, 'email', 'password')
        ;
        $this->auth->add('auth/Controller_Cookie');

        if(!$this->auth->model['id']){
            $hash = $this->hg_cookie->getLoginHash();
            if($hash){
                $u = $this->add('Model_User')->tryLoadBy('hash',$hash);
                if($u->loaded()){
                    $this->auth->login($u["email"]);
                }
            }
        }
        $this->user_access->setUser($this->currentUser());
    }
    function getUserType(){
    	if ($this->currentUser()->canBeManager()) return 'manager';
        if ($this->currentUser()->canBeSales()) return 'sales';
    	if ($this->currentUser()->canBeDeveloper()) return 'team';
    	if ($this->currentUser()->canBeClient()) return 'client';
    	if ($this->currentUser()->canBeAdmin()) return 'admin';
    	//if ($this->auth->model['is_system']) return 'system';
    }

    function getCurrentUserRole(){
        return $this->layout->rm->getCurrentUserRole();
    }

    function siteURL(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
    }

    function autoLogin() {
        // Autologin from admin/users
        if( (isset($_GET['id'])) && (isset($_GET['hash'])) ){
            $u=$this->add('Model_User')->getActive();
			$u->load($_GET["id"]);
            if($u['hash']!=$_GET["hash"]){
                echo json_encode("Wrong user hash");
                $this->logVar('wrong user hash: '.$u['hash']);
                exit;
            }
            unset($u['password']);
            $this->auth->addInfo('user',$u);
            $this->auth->login($u['email']);
            if($_GET['clear']==1){
                setcookie("fuser", "", time()-3600);
                setcookie("fhash", "", time()-3600);
            }
        }
    }

    function defineAllowedPages() {
        // Allowed pages for guest
        $this->addAllowedPages(array(
            'index', 'intro', 'denied','test','api',
        ));

        // For Guests
        if (!$this->auth->isLoggedIn()){
            $this->addAllowedPages(array(
                'quotation','quotation2',
            ));
            if(!$this->auth->isPageAllowed($this->page)){
                $this->redirect('index');
            }
        } else {
            if (!$this->currentUser()->canBeSystem()) {
                // Access for all non-system roles
                $this->addAllowedPages(array(
                    'quotation','quotation2','account', 'about', 'home', 'quotes','clients','projects','tasks','tasks2','deleted','developers','users','dashboard','trace','task'
                ));
                // Grant access for non-client users
                if($this->user_access->canSeeReportList()){
                    $this->addAllowedPages(array(
                        'reports'
                    ));
                }
                // Grant access for Financial Manager
                if($this->currentUser()->canSeeFinance()){
                    $this->addAllowedPages(array(
                        'rates'
                    ));
                }
            } else {
                $this->addAllowedPages(array(
                    'home','system','about','dashboard'
                ));
            }
            if( ($this->auth->isLoggedIn()) && ($this->user_access->canSeeLogs()) ) {
                $this->addAllowedPages(array(
                    'logs'
                ));
            }
            $this->addAllowedPages(array(
                'api','clients2','settings'
            ));
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

    function initLayout(){
        try {
            if(!$this->auth->isPageAllowed($this->page)){
                throw $this->exception('This user cannot see this page','Exception_Denied');
            }
            parent::initLayout();
        } catch (Exception_Denied $e) {
            // TODO show denied page
            //throw $e;
            $v = $this->layout->add('View')->addClass('denied');
            $v->add('View')->setElement('h2')->set('You cannot see this page');
            $v->add('View_Error')->set('Try to change role if you have multiple roles for this account');
        }
    }
    protected function addRouter() {
        $this->url_page = $this->page;
        $this->add('Controller_PatternRouter');
        $this->router->addRule('(quotes)\/([\d]+)','quotes_one',array('quotes','id'));
        //$this->router->link('quotes',array('quote_id'));
        $this->router->route();
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
        if(is_array($string)){
            return $string;
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
