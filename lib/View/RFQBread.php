<?php
class View_RFQBread extends View {
    function init(){
        parent::init();

    	$this->add('x_bread_crumb/View_BC',array(
    			'routes' => array(
    					0 => array(
    							'name' => 'Home',
    					),
    					1 => array(
    							'name' => 'Quotes',
    							'url' => $this->quotes_link,
    					),
    					2 => array(
    							'name' => 'Details of Quotation (requirements)',
    							'url' => '',
    					),
    			)
    	));
    	$this->add('P');
    }
}
