<?php
class Page_index extends Page {
    function init(){
        parent::init();

        $this->template->trySet('guest_quotation_link',$this->app->url('/quotation'));

        $form=$this->add('Frame')->setTitle('Client Log-in')->add('Form');
        $form->addClass('stacked');
        $form->addField('line','email')->js(true)->focus();
        $form->addField('password','password');
        $form->addField('Checkbox','memorize','Remember me');
        $form->addSubmit('Login');

        if($form->isSubmitted()){
            $l=$form->get('email');
            $p=$form->get('password');

            $url = 'v1/auth/login/';
            $data = array('u'=>$l,'p'=>$p);
            $res = json_decode($this->app->do_post_request($url,$data));

            if($res->result == 'success') {
                if($form->get('memorize') == true) $expire_days = 365; else $expire_days = 1;
                setcookie($this->app->name."_auth_token", $res->hash->lhash, time()+60*60*24*$expire_days);
                $form->js()->redirect('dashboard')->execute();
                $this->js()->redirect($this->app->url('testapi',array('message' => 'user logged in and have got lhash=' . $res->lhash, 'lhash' => $res->lhash)))->execute();
            } else {
                $form->getElement('password')->displayFieldError($res->message);
            }
        }
    }
    function defaultTemplate(){
        return array('page/index');
    }
}
