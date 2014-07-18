<?php
class page_account extends Page {
    function init(){
        parent::init();

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Settings',
                    'url' => 'account',
                ),
            )
        ));

        // Left side form
        $v=$this->add('View')->setClass('span_6 col')->addStyle('margin-top:20px;');
		$v->add('H3')->set('Personal Data Form');
        $f=$v->add('Form');
        $user_model=$f->setModel($this->add('Model_User')->getActive()->load($this->api->auth->model['id']),array('name'));
        $f->addSubmit('Save');
        if($f->isSubmitted()){
            $f->update();
            $f->js()->univ()->successMessage('Successfully updated your details')->execute();
        }

        $u=$this->add('Model_User')->getActive()->load($this->api->auth->model['id']);
        
        $v=$this->add('View')->setClass('span_6 col')->addStyle('margin-top:20px;');
		$v->add('H3')->set('Change Password');
        $f=$v->add('Form');
        $f->addField('password','pp','Old Password')->validateNotNULL();
        $f->addField('password','p1','New Password')->validateNotNULL();
        $f->addField('password','p2','Verify');
        $f->addSubmit('Change Password');
        if($f->isSubmitted()){
            //echo $this->api->auth->encryptPassword($f->get('pp')).'=='.$u['email'].$u['password'];
            if($this->api->auth->encryptPassword($f->get('pp'))!=$u->get('password')){
                $f->getElement('pp')->displayFieldError('Old password is incorrect');
                return;
            }

            if($f->get('p1')!=$f->get('p2')){
                $f->getElement('p2')->displayFieldError('Password don\'t match');
                return;
            }

            $u->set('password',$f->get('p1'))->update();
            $f->js()->univ()->successMessage('password changed')->getjQuery()->reload()->execute();
        }

		$v=$this->add('View')->setClass('span_6 col')->addStyle('margin-top:20px;');
		$v->add('H3')->set('Mail Settings');
        $f=$v->add('Form');
        $f->setModel($this->add('Model_User')->getActive()->load($this->api->auth->model['id']),array('mail_task_changes'));
        $f->addSubmit('Save');
        if($f->isSubmitted()){
            $f->update();
            $f->js()->univ()->successMessage('Successfully updated your details')->execute();
        }

		$v=$this->add('View')->setClass('span_6 col')->addStyle('margin-top:20px;');

    }
}
