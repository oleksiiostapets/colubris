<?php
class View_DeletedTabs extends View {
    function init(){
        parent::init();
        $tabs = $this->add('Tabs');

        $tabs->addTabUrl('./projects','Projects');
        $tabs->addTabUrl('./quotes','Quotes');
        $tabs->addTabUrl('./tasks','Tasks');
        $tabs->addTabUrl('./users','Users');
        $tabs->addTabUrl('./clients','Clients');
    }
}
