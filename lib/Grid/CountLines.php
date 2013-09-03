<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/26/13
 * Time: 3:05 PM
 * To change this template use File | Settings | File Templates.
 */
class Grid_CountLines extends Grid {
    function init() {
        parent::init();
        $this->addClass('zebra bordered');
        $this->addColumn('text','count')->setCaption('#');
    }
    public $number_count = 0;
    function formatRow() {
        parent::formatRow();
        $this->current_row['count'] = ++$this->number_count;
    }
}