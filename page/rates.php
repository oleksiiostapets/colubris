<?php

class page_rates extends Page {
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeRates() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
    }

    function page_index() {
        $model=$this->add('Model_Rate');
        $model->setOrder('from');
        $cr=$this->add('CRUD');
        $cr->setModel($model,
            array('from','to','value')
        );
    }
}
