<?php
class page_api_account extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_User');
    }
    function page_changePassword(){
        $id = $this->checkGetParameter('id');
        $this->m->tryLoad($id);
        if(!$this->m->loaded()){
            echo json_encode([
                'result' => 'error',
                'error_message' => 'Record with the id was not found',
            ]);
            exit();
        }
        // Validations
        $old_password = $this->checkGetParameter('old_password');
        $new_password = $this->checkGetParameter('new_password');
        $verify_password = $this->checkGetParameter('verify_password');

        $validation_errors = array();

        if(trim($old_password) == '' || $old_password == 'undefined') $validation_errors[] = array('old_password' => 'required');
        if(trim($new_password) == '' || $new_password == 'undefined') $validation_errors[] = array('new_password' => 'required');
        if(trim($verify_password) == '' || $verify_password == 'undefined') $validation_errors[] = array('verify_password' => 'required');

        if($new_password != $verify_password) $validation_errors[] = array('verify_password' => 'passwords don\'t match');

        $u=$this->app->auth->model;
        if(!$this->app->auth->verifyCredentials($this->m->get('email'),$old_password)) $validation_errors[] = array('old_password' => 'incorrect');

        if (count($validation_errors) > 0){
            echo json_encode([
                'result' => 'validation_error',
                'errors' => $validation_errors,
            ]);
            exit();
        }

        $this->m->set('password',$new_password);
        $this->m->save();
        echo json_encode([
            'result' => 'success',
            'data' => $this->m->get(),
        ]);
        exit;
    }

}