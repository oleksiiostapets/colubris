<?php
class Page_index extends Page {
    function init(){
        parent::init();

        if($this->api->auth->isLoggedIn())$this->api->redirect('dashboard');

        $this->template->trySet('guest_quotation_link',$this->api->url('/quotation'));

        $form=$this->add('Frame')->setTitle('Client Log-in')->add('Form');
        $form->addField('line','email')->js(true)->focus();
        $form->addField('password','password');
        $form->addField('Checkbox','memorize','Remember me');
        $form->addSubmit('Login');
        $form->setFormClass('vertical');
        $auth=$this->api->auth;
        if($form->isSubmitted()){
            $l=$form->get('email');
            $p=$form->get('password');

            if($auth->verifyCredentials($l,$p)){
                $auth->login($l);
                if($form->get('memorize') == true){
                	setcookie("colubris_auth_useremail",$form->get('email'),time()+60*60*24*30*6);
                }
                
                $form->js()->univ()->redirect('dashboard')->execute();
            }
            $form->getElement('password')->displayFieldError('Incorrect login');
        }
    }
    function defaultTemplate(){
        return array('page/index');
    }
}
