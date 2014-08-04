<?php
class endpoint_v1_client extends Endpoint_v1_General {

    public $model_class = 'Client';

    function init() {
        parent::init();
    }
    public function page_getForClient(){
//        $client_id = $this->getClientId();
//        $data = $this->model->addCondition('id',$client_id)->getRows();
        $data = $this->model->getRows();
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