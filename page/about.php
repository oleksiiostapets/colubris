<?php
class page_about extends Page {
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



        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'About',
                    'url' => 'about',
                ),
            )
        ),'bread_crumb');
    }
    function defaultTemplate() {
        return array('page/about');
    }
}
