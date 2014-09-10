<?php

class page_rates extends Page {
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeRates() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
        $this->title = 'Rates';

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Rates',
                    'url' => 'rates',
                ),
            )
        ),'bread_crumb');
    }

    function page_index() {
        $model=$this->add('Model_Rate');
        $model->setOrder('from');
        $cr=$this->add('CRUD');
        $cr->setModel($model,
            array('from','to','value')
        );
    }
    function defaultTemplate() {
        return array('page/page');
    }
}
