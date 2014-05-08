<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/30/13
 * Time: 5:54 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_RFQ extends Page {

    function init() {
        parent::init();
        if (!$this->app->user_access->canSendRequestForQuotation()) {
            throw $this->exception('This user cannot send quotation request','Exception_Denied');
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
                    'url' => 'quotes',
                ),
                2 => array(
                    'name' => 'Request for Quotation (create)',
                    'url' => 'quotes/rfq',
                ),
            )
        ));
    }
}