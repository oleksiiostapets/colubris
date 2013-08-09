<?php

class page_team_reports extends page_reportsfunctions {

    function initMainPage() {
        $this->add('View_ReportsSwitcher');

        $this->add('View_Report');
    }

}
