<?php
class Page_index extends Page {
    function init(){
        parent::init();

        if($this->app->auth->isLoggedIn())$this->app->redirect('dashboard');

        $this->template->trySet('guest_quotation_link',$this->app->url('/quotation'));

        $form=$this->add('Frame')->setTitle('Client Log-in')->add('Form');
        $form->addClass('stacked');
        $form->addField('line','email')->js(true)->focus();
        $form->addField('password','password');
        $form->addField('Checkbox','memorize','Remember me');
        $form->addSubmit('Login');
//        $form->setFormClass('vertical');
        $auth=$this->app->auth;
        if($form->isSubmitted()){
            $l=$form->get('email');
            $p=$form->get('password');

            $absolute_url = $this->app->full_url($_SERVER);
            $url = $absolute_url.'/api/?page=v1/auth/login/';
            $data = array('u'=>$l,'p'=>$p);
            $res = json_decode($this->app->do_post_request($url,$data));
            //var_dump($res);exit;
            if($res->result == 'success') {
                setcookie(($this->app->name."_auth_token", $res->hash->lhash, time()+60*60*24*30*12);
                $form->js()->redirect('dashboard')->execute();
                $this->js()->redirect($this->app->url('testapi',array('message' => 'user logged in and have got lhash=' . $res->lhash, 'lhash' => $res->lhash)))->execute();
            } else {
                $form->getElement('password')->displayFieldError($res->message);
            }

//            if($auth->verifyCredentials($l,$p)){
//                $auth->login($l);
//                if($form->get('memorize') == true){
//                    $hash = $this->app->hg_cookie->rememberLoginHash($form->get('email'),true);
//                    $u=$this->add('Model_User')->notDeleted()->tryLoadBy('email',$form->get('email'));
//                    if($u->loaded()){
//                        $u->set('hash',$hash);
//                        $u->saveAndUnload();
//                    }
//                	//setcookie("colubris_auth_useremail",$form->get('email'),time()+60*60*24*30*6);
//                }
//
//                $form->js()->redirect('dashboard')->execute();
//            }
//            $form->getElement('password')->displayFieldError('Incorrect login');
        }
    }
    function defaultTemplate(){
        return array('page/index');
    }
}
