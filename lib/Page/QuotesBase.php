<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/29/13
 * Time: 1:02 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_QuotesBase extends Page {
    public $quote;
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeQuotesList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }


        $this->quote = $this->add('Model_Quote')->setOrder('updated_dts');//->debug();

        if ($this->api->currentUser()->isCurrentUserClient()) {
            // show only client's quotes
            $pr = $this->quote->join('project','project_id','left','_pr');
            $pr->addField('pr_client_id','client_id');
            $this->quote->addCondition('pr_client_id',$this->api->auth->model['client_id']);
        }

        if ($this->api->currentUser()->isCurrentUserDev()) {
            // developer do not see not well prepared (quotation_requested status) and finished projects
            $this->quote->addCondition('status',array(
                'estimate_needed','not_estimated','estimated','estimation_approved'
            ));
        }
    }
    function page_index() {
        $this->addBreadCrumb($this);
        $this->add('H1')->set('Quotes');
        $this->addRequestForQuotationButton($this);
        $this->addQuotesCRUD($this);
    }

    function addBreadCrumb($view) {
        $view->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'quotes',
                ),
            )
        ));
    }

    function addRequestForQuotationButton($view) {
        if ($this->api->currentUser()->canSendRequestForQuotation()) {
            $b = $view->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->api->url('quotes/rfq'))
            ));
        }
    }

    function addQuotesCRUD($view) {
        $user = $this->api->currentUser();
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Quotes',
            'allow_add'       => false,
            'allow_edit'      => $this->quote->canUserEditQuote($user),
            'allow_del'       => $this->quote->canUserDeleteQuote($user),
            'allowed_actions' => $this->quote->userAllowedActions($user),
        ));

        $cr->setModel(
            $this->quote,
            $this->quote->whatQuoteFieldsUserCanEdit($user),
            $this->quote->whatQuoteFieldsUserCanSee($user)
        );
    }
}