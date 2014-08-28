<?php
class page_testapi extends Page {
    function init() {
        parent::init();

    }

    function page_index() {
        if($_GET['message']) $this->add('View')->set($_GET['message']);
        if($_GET['lhash']) $this->add('View')->setHtml('<a href="'.$this->app->url('testapi/check',array('lhash'=>$_GET['lhash'])).'">Check</a>');

        $f = $this->add('Form');
        $f->addField('u');
        $f->addField('p');
        $f->addSubmit('Send');

        if ($f->isSubmitted()){
            //var_dump($f->get());
            //$url = 'http://localhost/colubris43/api/?page=v1/user/login/';
            $absolute_url = $this->app->full_url($_SERVER);
            $url = $absolute_url.'/api/?page=v1/user/login/';
            $data = array('u'=>$f->get('u'),'p'=>$f->get('p'));
            $res = $this->app->do_post_request($url,$data);
            if($res == 'false') {
                $this->js()->redirect($this->app->url('testapi',array('message' => 'wrong login')))->execute();
            } else {
                $res = json_decode($res);
                $this->js()->redirect($this->app->url('testapi',array('message' => 'user logged in and have got lhash=' . $res->lhash, 'lhash' => $res->lhash)))->execute();
            }
        }
    }
    function page_check(){
        //$url = 'http://localhost/colubris43/api/?page=v1/user/check&lhash='.$_GET['lhash'];
        $absolute_url = $this->full_url($_SERVER);
        $url = $absolute_url.'/api/?page=v1/user/check&lhash='.$_GET['lhash'];
        $data = array('lhash'=>$_GET['lhash']);
        $res = $this->do_get_request($url,$data);

        if($res == 'true') $this->add('View')->set('lhash valid');
        else $this->add('View')->set('lhash invalid');
    }

}
