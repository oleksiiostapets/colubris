<?php
class page_client extends Page {
    function init(){
        parent::init();

        $t=$this->add('H1');
        $t->set('Quotes');

        $q=$this->add('Client_Quotes');
    }
}
