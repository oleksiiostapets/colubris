<?php
class TestApi extends ApiFrontend {
    function init(){
        parent::init();
        $this->pathfinder->addLocation('.',array(
            'addons'=>array(
                'atk4-addons',
//                '../addons'
            ),
            'php'=>array(
                'lib',
                'atk4-addons',
                'atk4-addons/misc/lib',
                'vendor',
            ),
            'js'=>array('../public/atk4/js'),
            'css'=>array('../public/atk4/css'),
            'mail'=>array('atk4/mail','templates/mail'),
        ))
            ->setBasePath(getcwd())
            ->setBaseURL($this->api->url('/'))
        ;

//	    $this->addAuth();
        $this->dbConnect();
        $this->add('jUI');
        $m = $this->add('Menu', null, 'Menu');
        $m->addMenuItem('index', 'Back');
    }
    function page_index($page){
        $l = $this->add('Grid');
        $l->setModel('AgileTest');
        $l->addTotals()->setTotalsTitle('name', '%s test%s');
        
        $l->addHook('formatRow', function($l){
            $n = $l->current_row['name'];
            $n = str_replace('.php', '', $n);
            $n = '<a href="'.$l->api->url($n).'">'.$n.'</a>';
            $l->current_row_html['name'] = $n;
        });
    }
	private function addAuth() {
		$this->auth = $this->add('Auth');
		$this->auth->usePasswordEncryption('md5');
		$this->auth->setModel('Model_User', 'email', 'password');
	}
}
