<?php
class page_intro extends Page {
    function init(){
        parent::init();

        $t=$this->add('Tabs');

        $t->addTabURL('./concept','What is Budget?');
        $t->addTabURL('./development','How Developers Work?');
        $t->addTabURL('./tracking','Progress Tracking');
        $t->addTabURL('./timesheets','Timesheets');
        $t->addTabURL('./changereq','Change Requests');
        $t->addTabURL('./tasks','Task Lists');
        $t->addTabURL('./qa','Quality Assurance');
    }
}
