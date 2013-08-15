<?php

class page_team_reports extends page_reportsfunctions {
    function init() {
        parent::init();

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Reports',
                    'url' => 'team/reports',
                ),
            )
        ));
    }

    function initMainPage() {
        $this->add('View_ReportsSwitcher');

        $this->add('View_Report',array('grid_show_fields'=>array('project','quote','name','status','type','estimate','spent','date','performer')));
    }

}
