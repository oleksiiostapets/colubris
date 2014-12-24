<?php
class endpoint_v1_account extends Endpoint_v1_General {

    public $model_class = 'User';

    function init() {
        parent::init();
    }
    function post_changePassword(){
        $id = $this->checkGetParameter('id');
        $this->model->tryLoad($id);
        if(!$this->model->loaded()){
            return [
                'result' => 'error',
                'error_message' => 'Record with the id was not found',
            ];
        }
        // Validations
        $old_password = $this->getParameter('old_password');
        $new_password = $this->getParameter('new_password');
        $verify_password = $this->getParameter('verify_password');

        $validation_errors = array();

        if(trim($old_password) == '' || $old_password == 'undefined') $validation_errors[] = array('old_password' => 'required');
        if(trim($new_password) == '' || $new_password == 'undefined') $validation_errors[] = array('new_password' => 'required');
        if(trim($verify_password) == '' || $verify_password == 'undefined') $validation_errors[] = array('verify_password' => 'required');

        if($new_password != $verify_password) $validation_errors[] = array('verify_password' => 'passwords don\'t match');

        $u=$this->app->auth->model;
        if(!$this->app->auth->verifyCredentials($this->model->get('email'),$old_password)) $validation_errors[] = array('old_password' => 'incorrect');

        if (count($validation_errors) > 0){
            return [
                'result' => 'validation_error',
                'errors' => $validation_errors,
            ];
        }

        $this->model->set('password',$new_password);
        $this->model->save();
        return [
            'result' => 'success',
            'data' => $this->model->get(),
        ];
    }

    function post_addToFilestore(){

        $id = $this->checkPostParameter('id');
        $this->model->tryLoad($id);
        if(!$this->model->loaded()){
            return [
                'result' => 'error',
                'error_message' => 'Record with the id was not found',
            ];
        }
        if($_FILES['file']){
            $url = $this->app->pathfinder->base_location->getPath().'/../lib/External/MimeReader.php';
            require_once($url);

            $tmp_name = $_FILES["file"]["tmp_name"];
            $name = $_FILES["file"]["name"];
//            $img_url = $_SERVER["DOCUMENT_ROOT"] . $this->app->getBaseUrl() . "upload/" . $name;
            $img_url = $this->app->pathfinder->base_location->getPath().'/../upload/' . $name;
//            var_dump($img_url);exit;
            move_uploaded_file($tmp_name, $img_url);

            $mime = new MimeReader($img_url);
            $mime_type_string = $mime->get_type();

            $up_model = $this->add('filestore/Model_Image');
            $up_model->set('filestore_volume_id',$up_model->getAvailableVolumeID());
            $up_model->set('original_filename',basename($img_url));
            $type = $up_model->getFiletypeID($mime_type_string);
            $up_model->set('filestore_type_id',$type);
            $up_model->import($img_url,'copy');
            try {
                $up_model->save();
                unlink($img_url);
                $this->model->set('avatar_id',$up_model->get('id'));
                $this->model->save();

                return [
                    'result' => 'success',
                    'data' => $up_model->get(),
                    'data' => 'adnfbdv',
                ];
            } catch (Exception $e) {
                $this->app->logger->logCaughtException($e);
                /*echo $e->getMessage()."\n";
                echo $e->getFile()."\n";
                echo $e->getLine()."\n";
                echo $e->getTraceAsString();*/
            }
            return [
                'result' => 'error',
                'error_message' => 'File was not imported into filestore',
            ];
        } else {
            return [
                'result' => 'error',
                'error_message' => 'File not found',
            ];
        }
    }

}