<?php
class page_system_organisation extends Page {
    function init(){
        parent::init();

    }
    function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Organisation',
                    'url' => 'system/organisation',
                ),
            )
        ));

        $this->add('H1')->set('Organisation');

        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_Organisation')->setOrder('name');

        $crud->setModel($model,
            array('name','desc'),
            array('name','desc')
        );
    }
}
