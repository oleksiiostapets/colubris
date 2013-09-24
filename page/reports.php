<?php
class page_reports extends Page {
    function page_index(){

        // Checking client's read permission to this quote and redirect to denied if required
        //if( !$this->api->currentUser()->canUserMenageClients() ){
        //    throw $this->exception('You cannot see this page','Exception_Denied');
        //}

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Reports',
                    'url' => 'reports',
                ),
            )
        ));

        $this->add('View_ReportsSwitcher');
        $this->add('View_Report',array('grid_show_fields'=>$this->getGridFields(),'export_fields'=>$this->getExportFields()));

    }
    function getGridFields() {
        if ($this->api->currentUser()->canBeClient()) {
            return array('project_name','task_name','status','type','estimate','spent_time','date');
        }
        if ($this->api->currentUser()->canBeDeveloper()) {
            return array('project_name','task_name','status','type','estimate','spent_time','date','user');
        }
        if ($this->api->currentUser()->canBeManager()) {
            return array('project_name','task_name','status','type','estimate','spent_time','date','user');
        }
    }
    function getExportFields() {
        return array('project_name','task_name','status','type','estimate','spent_time','date');
    }
    function page_more(){
        if (!$_GET['task_time_id']) {
            throw $this->exception('task_time_id must be provided!');
        }
        $this->api->stickyGET('task_time_id');
        $task_time=$this->add('Model_TaskTime')->load($_GET['task_time_id']);
        $task=$this->add('Model_Task')->load($task_time['task_id']);


        $v = $this->add('View');

        // Description
        $descr_view = $v->add('View')->addClass('span12');
        $descr_view->add('H4')->set('Description');
        $descr_view->add('View')->setHtml( $this->api->colubris->makeUrls(nl2br($task->get('descr_original'))) );

    }
}