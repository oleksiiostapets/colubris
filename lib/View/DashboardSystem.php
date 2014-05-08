<?php
class View_DashboardSystem extends View {
    public $allow_add  = false;
    public $allow_edit = false;
    public $allow_del  = false;
    function init(){
        parent::init();

        $this->add('P');
        $this->add('H1')->set('Quotes');
        $quotes = $this->add('Model_Quote');
        $quotes->addCondition('organisation_id','<',1);

        $this->addQuotesCRUD($this);
    }
}
