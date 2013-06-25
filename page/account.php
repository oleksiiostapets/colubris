<?php
class page_account extends Page {
    function init(){
        parent::init();

        $l=$this->api->add('Columns');

        // Left side form
        $col=$l->addColumn('30%');
        $f=$col->add('Form');
        $user_model=$f->setModel($this->add('Model_User')->load($this->api->auth->model['id']),array('name'));
        $f->addSubmit('Save');
        if($f->isSubmitted()){
            $f->update();
            $f->js()->univ()->successMessage('Successfully updated your details')->execute();
        }

        $u=$this->add('Model_User')->load($this->api->auth->model['id']);
        $col=$l->addColumn('30%');
        $f=$col->add('Form');
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
    }
}
