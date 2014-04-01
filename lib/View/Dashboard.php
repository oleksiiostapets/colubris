<?php
class View_Dashboard extends View {

    function init(){
        parent::init();

        // admin and sys have no dash
        if ( $this->app->currentUser()->isAdmin() || $this->app->currentUser()->isSystem() ) {
            $this->template->del('delete_dash');
            return;
        }

        $this->addClientActionBlock();
        $this->addQuoteComments();
        $this->addTaskComments();
        $this->addTasks();
    }
    function defaultTemplate() {
        return array('view/dashboard');
    }

    /* ************************************************************************************
     *  Block with action for Client
     */
    protected function addClientActionBlock() {
        if ($this->app->currentUser()->isClient()) {
            $client_row = $this->add('View',null,'ClientActions')->addClass('atk-box ui-helper-clearfix');
            $client_row->add('H3')->setText('Actions:');
            $b = $client_row->add('Button')->set('Request For Quotation');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->app->url('quotes/rfq'))
            ));

            $b = $client_row->add('Button')->set('Create Task');
            $b->addStyle('margin-bottom','10px');
            $b->js('click', array(
                $this->js()->univ()->redirect($this->app->url('tasks/new'))
            ));
        }
    }

    /* ************************************************************************************
     *  Block with comments on quotes requirements which user have access to
     */
    protected function addQuoteComments() {
        $this->add('View_Dashboard_QuoteComments',null,'crud_com_quote');
    }

    /* ************************************************************************************
     *  Block with comments on requirements tasks which user have access to
     */
    protected function addTaskComments() {
        $this->add('View_Dashboard_TaskComments',null,'crud_comm_tasks');
    }

    /* ************************************************************************************
     *  Block with tasks which user have access to
     */
    protected function addTasks() {

        $cr = $this->add('CRUD_Task',array(
            'items_per_page'  => 10,
            'allow_add'  => false,
            'allow_edit' => false,
            'allow_del'  => true
        ),'crud_active_tasks');

        $cr->setModel(
            $this->add('Model_Task')->addDashCondition(),
            $this->app->user_access->getDashboardFormFields(),
            $this->app->user_access->getDashboardGridFields()
        );
        $cr->configure();
    }
}
