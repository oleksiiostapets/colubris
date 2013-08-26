<?php

class page_client_reports extends page_reportsfunctions {

    function page_index() {
        $this->add('View_ReportsSwitcher');

        $this->add('View_Report',array('grid_show_fields'=>array('project','quote','name','status','type','estimate','spent','date')));
    }

}
