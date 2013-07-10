<?php
class page_manager_quotes extends Page {
    function init(){
        parent::init();

        $t=$this->add('H1');
        $t->set('Quotes');

        $q=$this->add('Manager_Quotes');
    }
}
