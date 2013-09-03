<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/30/13
 * Time: 6:35 PM
 * To change this template use File | Settings | File Templates.
 */
class Form_RFQ extends Form {
    function init() {
        parent::init();
        $this->setModel('Model_Quote',array('project_id','name','general_description'));

        $this->getElement('project')->set($_GET['project']);
        $this->getElement('project_id')->set($_GET['project_id']);
        $this->getElement('project')->caption='Project\'s name';
        $this->getElement('name')->caption='Quotation\'s name';

        // TODO open frameURL instead of redirect
        $add_button = $this->add('Button')->set("New Project")->addClass('add_project');
        $add_button->js('click',
                    $this->js()->univ()->redirect($this->api->url('manager/project/add',array('return'=>'manager/quotes/rfq')))
                );


        $this->add('Order')->move($add_button,'before','name')->now();

        $this->addSubmit('To the Next Step');
        $this->onSubmit(array($this,'checkSubmited'));
    }
    function checkSubmited() {
        $js=array();
        $this->model->set('user_id',$this->api->auth->model['id']);
        $this->model->set('status','quotation_requested');
        $this->update();
        $this->api->redirect($this->api->url('/manager/quotes/rfq/requirements',array('quote_id'=>$this->model->get('id'))));
    }
}