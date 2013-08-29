<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/29/13
 * Time: 1:02 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_Quotes extends Page {
    function page_index() {

//        if (!isset($_GET['id'])) {
//            throw $this->exception('No $_GET[\'id\']');
//        }
//        $this->api->stickyGET('id');

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
                    'url' => $this->role.'/quotes',
                ),
            )
        ));
    }

    function addRequestForQuotationButton($view) {
        if ($this->api->currentUser()->canSendRequestForQuotation()) {
            $b = $view->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->api->url($this->role.'/quotes/rfq'))
            ));
        }
    }

    public $allow_add  = false;         // TODO move to model !!!
    public $allow_edit = false;         // TODO move to model !!!
    public $allow_del  = false;         // TODO move to model !!!
    public $allowed_actions  = array(); // TODO move to model !!!
    public $form_fields  = array();
    public $grid_fields  = array();
    function addQuotesCRUD($view) {
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Quotes',
            'allow_add'       => $this->allow_add,
            'allow_edit'      => $this->allow_edit,
            'allow_del'       => $this->allow_del,
            'role'            => $this->role,
            'allowed_actions' => $this->allowed_actions
        ));
        $m=$this->add('Model_Quote');

        // show only client's quotes
        if ($this->api->currentUser()->isClient()) {
            $pr = $m->join('project','project_id','left','_pr');
            $pr->addField('pr_client_id','client_id');
            $m->addCondition('pr_client_id',$this->api->auth->model['client_id']);
        }

        $cr->setModel( $m, $this->form_fields, $this->grid_fields );
    }
}