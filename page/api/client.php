<?php
class page_api_client extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Client');
    }
    public function page_getForClient(){
        $data = $this->m->getRows();
        echo json_encode([
            'result' => 'success',
            'data'   => $data,
        ]);
        exit();
    }

}