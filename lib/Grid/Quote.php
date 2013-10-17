<?php
class Grid_Quote extends Grid {
    function init() {
        parent::init();

        $this->addClass('zebra bordered');

    }
    function formatRow() {
    	parent::formatRow();

        $this->current_row_html['general_description']=nl2br($this->current_row_html['general_description']);
    }
}
