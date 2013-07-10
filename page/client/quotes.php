<?php
class page_client_quotes extends Page {
    function init(){
        parent::init();

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'client/quotes',
                ),
            )
        ));

        $t=$this->add('H1');
        $t->set('Quotes');

        $q=$this->add('Client_Quotes');
    }
}
