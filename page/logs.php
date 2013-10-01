<?php

class page_logs extends Page {
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeLogs() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }
    }

    function page_index() {
        $model=$this->add('Model_Log');
        $model->setOrder('created_at DESC');
        $grid=$this->add('Grid_Logs');
        $grid->setModel($model);

        $grid->addFormatter('new_data','data');
        $grid->addFormatter('changed_fields','changed_fields');
        $grid->addFormatter('new_data','wrap');
        $grid->addClass('zebra bordered');
        $grid->addQuickSearch(array('new_data','class'));
        $grid->addPaginator(5);
    }
}
