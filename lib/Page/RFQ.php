<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/30/13
 * Time: 5:54 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_RFQ extends Page {
    public $role = '';

    function init() {
        parent::init();
        if (!$this->api->currentUser()->canSendRequestForQuotation()) {
            throw $this->exception('This user cannot send quotation request');
        }
    }

    function page_index(){
        $this->addBreacrumb($this);
        $this->add('H1')->set('New Request for Quotation');
        $this->add('Form_RFQ');
    }


    function addBreacrumb($view){
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => $this->role.'/quotes',
                ),
                2 => array(
                    'name' => 'Request for Quotation (create)',
                    'url' => 'manager/quotes/rfq',
                ),
            )
        ));
    }
}