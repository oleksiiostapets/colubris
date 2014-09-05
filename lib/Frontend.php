<?php
class Frontend extends ApiFrontend {
    public $current_user;

    function getVer(){
        return 2.2;
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
            $this->redirect('index');
//        	setcookie("colubris_auth_useremail", "", time()-3600);
        }

        $this->checkAuth();

        $this->layout = $this->add('Layout_Fluid')->addClass('atk-swatch-ink');
        $this->template->set('page_title','Colubris');
        $this->layout->template->set('page_title','Colubris');

        $view_header = $this->layout->add('View',null,'Header_Content',array('view/header'));

        if($this->currentUser()){
            $this->layout->rm = $view_header->add('RoleMenu', 'SubMenu','SubMenu');
            $view_header->add('MyMenu',null,'Main_Menu');
            //$this->add('MySubMenu', 'SubMenu', 'SubMenu');

            // show current user name
            $view_header->template->set('name',$this->currentUser()->get('name')?$this->currentUser()->get('name'):'Guest' . ' @ ' .'Colubris Team Manager, ver.'.$this->getVer());
        }

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
        
    }
    protected function checkAuth(){
        if(isset($_COOKIE[$this->app->name.'_auth_token'])){
            $url = 'v1/auth/check&lhash='.$_COOKIE[$this->app->name.'_auth_token'];
            $res = json_decode($this->do_get_request($url));
            if($res->result == 'error'){
                $this->current_user = false;
            }else{
                $this->current_user = $this->add('Model_User')->load($res->user->id);
                $this->user_access->setUser($this->currentUser());
            }
        }
    }
    protected function addControllers() {
        $this->colubris    = $this->add('Controller_Colubris');
        $this->formatter   = $this->add('Controller_Formatter');
        $this->mailer      = $this->add('Controller_Mailer');
        $this->hg_cookie   = $this->add('Controller_MyCookie');
        $this->user_access = $this->add('Controller_UserAccess');
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

    function currentUser() {
        return $this->current_user;
    }

    function defineAllowedPages() {
        // Allowed pages for guest
        $this->addAllowedPages(array(
            'index', 'intro', 'denied','logout','test','api','testapi'
        ));

        // For Guests
        if (!$this->currentUser()){
            $this->addAllowedPages(array(
                'quotation','quotation2',
            ));
            if(!$this->isPageAllowed($this->page)){
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
            if($this->user_access->canSeeLogs()) {
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
    }

    function isPageAllowed($page){
        if(in_array($page, $this->allowed_pages)) return true; else return false;
    }
    function initLayout(){
        try {
            if(!$this->isPageAllowed($this->page)){
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

    // NET functions
    function do_post_request($url, $data, $optional_headers = null) {
        $url = $this->getConfig('php_api_base_url').$url;
        $data = http_build_query($data);
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data
        ));

        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        return $response;
    }
    function do_get_request($url, $optional_headers = null) {
        $url = $this->getConfig('php_api_base_url').$url;
        $params = array('http' => array(
            'method' => 'GET'
        ));

        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        return $response;
    }
    function url_origin($s, $use_forwarded_host=false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }
    function full_url($s, $use_forwarded_host=false)
    {
        return $this->url_origin($s, $use_forwarded_host) . substr($s['REQUEST_URI'],0,strpos($s['REQUEST_URI'],'/public'));
    }
}
