<?php
class page_api_task extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Task');
    }

    function page_getStatuses(){
        $data = array();
        foreach ($this->m->task_statuses as $status){
            $data[] = array('id' => $status, 'name' => $status);
        }
        echo json_encode([
            'result' => 'success',
            'data'   => $data
        ]);
        exit();
    }
}