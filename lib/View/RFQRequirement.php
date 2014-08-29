<?php
class View_RFQRequirement extends View {
    function init(){
        parent::init();

        $this->add('P');
        $this->add('H4')->set('New Requirement:');
        
        $form=$this->add('Form');
        $m=$this->setModel('Model_Requirement')->notDeleted();
        $form->setModel($m,array('name','descr','file_id'));
        $form->addSubmit('Save');

        if($form->isSubmitted()){
        	$form->model->set('user_id',$this->app->currentUser()->get('id'));
        	$form->model->set('quote_id',$_GET['quote_id']);
        	$form->update();
        	$this->api->redirect(null);
        }
    }
}
