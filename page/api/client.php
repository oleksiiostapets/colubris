<?php
class page_api_client extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Client');
    }
    public function page_getForClient(){
//        $client_id = $this->getClientId();
//        $data = $this->m->addCondition('id',$client_id)->getRows();
        $data = $this->m->getRows();
        echo json_encode([
            'result' => 'success',
            'data'   => $data,
        ]);
        exit();
    }
    private function getClientId() {
        $client_id = $this->checkGetParameter('client_id'); // method from trait
//        var_dump($client_id);exit;
        return $client_id;
    }
}