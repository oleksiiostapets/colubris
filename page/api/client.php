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

    function page_addToFilestore(){
        $id = $this->checkPostParameter('id');
        $this->m->tryLoad($id);
        if(!$this->m->loaded()){
            echo json_encode([
                'result' => 'error',
                'error_message' => 'Record with the id was not found',
            ]);
            exit();
        }
        if($_FILES['file']){
            require_once('./lib/External/MimeReader.php');

            $tmp_name = $_FILES["file"]["tmp_name"];
            $name = $_FILES["file"]["name"];
            $img_url = $_SERVER["DOCUMENT_ROOT"] . $this->app->getBaseUrl() . "upload/" . $name;
            move_uploaded_file($tmp_name, $img_url);

            $mime = new MimeReader($img_url);
            $mime_type_string = $mime->get_type();

            $up_model = $this->add('filestore/Model_Image');
            $up_model->set('filestore_volume_id',$up_model->getAvailableVolumeID());
            $up_model->set('original_filename',basename($img_url));
            $type = $up_model->getFiletypeID($mime_type_string);
            $up_model->set('filestore_type_id',$type);
            $up_model->import($img_url,'copy');
            $up_model->save();

            unlink($img_url);

            $this->m->set('avatar_id',$up_model->get('id'));
            $this->m->save();

            echo json_encode([
                'result' => 'success',
                'data' => $up_model->get(),
            ]);
            exit;
        }else{
            echo json_encode([
                'result' => 'error',
                'error_message' => 'File not found',
            ]);
            exit();
        }
    }

}