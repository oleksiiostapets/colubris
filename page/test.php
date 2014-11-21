<?php
class Page_test extends Page {
    function init(){
        parent::init();

        $user = $this->app->currentUser();


        $l=$user->get('email');
        $p=$user->get('password');

        $url = 'v1/auth/login/';
        $data = array('u'=>$l,'p'=>$p);
        $res = json_decode($this->app->do_post_request($url,$data));
        var_dump($res);

    }
    /*function defaultTemplate(){
        return array('page/index');
    }*/
}
