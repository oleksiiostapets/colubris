<?php
/**
 * Created by Vadym Radvansky
 * Date: 8/4/14 4:25 PM
 */
class Endpoint_v1_General extends Endpoint_REST {


    use Helper_Url;

    protected $m;
    protected $count;
    protected $offset;
    protected $method;

    public $user_id_field = false;

    function init() {
        parent::init();

        $auth_res = $this->checkAuth();
        if (is_array($auth_res)) {
            echo json_encode([$auth_res]);
            exit;
        }

        $this->getParameter('count') ? $this->count = $this->getParameter('count') : $this->count = 9999999999;
        $this->getParameter('offset') ? $this->offset = $this->getParameter('offset') : $this->offset = 0;
        $this->getParameter('method') ? $this->method = $this->getParameter('method') : $this->method = false;
    }

    protected function _model()
    {

        try{
            $m  = parent::_model();
        }catch (Exception $e){
            $this->app->logger->logCaughtException($e);
            echo json_encode( [
                'result' => 'error',
                'code'    => '5399',
                'error_message'   => 'function _model() '.$e->getMessage(),
            ]);exit;
        }
        return $m;

    }

    protected function checkAuth() {
        $lhash = $this->checkGetParameter('lhash');
        if (is_array($lhash)) {
            return $lhash;
        }
        $current_user = $this->add('Model_User')->getByLHash($lhash);
        if (!$current_user->loaded()) {
            return [
                'result' => 'error',
                'code'    => '5300', // :)
                'message' => 'User cannot be authorized'
            ];
        } else if (strtotime($current_user['lhash_exp']) <= strtotime(date('Y-m-d G:i:s', time()))) {
            return [
                'result'  => 'error',
                'code'    => '5301', // :)
                'message' => 'User exist but lhash is out of date, get a new one.'
            ];
        }
        /*if(!$current_user->checkUserByLHash($lhash)) return [
            'result' => 'error',
            'message' => 'User cannot be authorized'
        ];*/

        $this->app->current_user = $current_user;
        return true;
    }

    /**
     * Parameters:
     * field - which field will be used for search (optional)
     * value - value for the field (optional)
     * count - count rows (optional)
     * offset - offset for query (optional)
     * method - for defining type of search. If empty - strong search, 'rlike', llike', 'alike'
     *
     * SELECT
     */
    function get_getByField() {
        try{
            $field = $this->getParameter('field');
            $value = $this->getParameter('value');
            if($field != ''){
                if($this->method){
                    if($this->method == 'rlike') $value = $value . '%';
                    if($this->method == 'llike') $value = '%' . $value;
                    if($this->method == 'alike') $value = '%' . $value . '%';
                    $this->model->addCondition($field,'LIKE',$value);
                }else{
                    $this->model->addCondition($field,$value);
                }
            }

            if ($this->model->hasElement('is_deleted')){
                $this->model->addCondition('is_deleted',false);
            }
            $this->model->dsql()->calcFoundRows();
            $this->model->setLimit($this->count,$this->offset);
//            $data = $this->model->getRowsForCurrentUser();
            if(method_exists($this->model,'prepareForSelect')){
                try {
                    $data = $this->model->prepareForSelect($this->app->current_user)->getRows();
                } catch (Exception_API_CannotSee $e) {
                    return [
                        'result' => 'error',
                        'code'    => '5310',
                        'message' => 'User has no right to see '.$this->model_class,
                    ];
                } catch (Exception $e){
                    return[
                        'result'  => 'error',
                        'code'    => '5399',
                        'message'   => $e->getMessage(),
                    ];
                }
            }else{
                $data = $this->model->getRows();
            }
            $total_rows = $this->model->dsql()->foundRows();
            return [
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
            ];
        }catch(Exception $e){
            return[
                'result'  => 'error',
                'code'    => '5399',
                'message'   => $e->getMessage(),
            ];
        }
    }
    /**
     * Parameters:
     * field1 - which field will be used for search (optional)
     * value1 - value for the field (optional)
     * ...
     * field5 - which field will be used for search (optional)
     * value5 - value for the field (optional)
     * count - count rows (optional)
     * offset - offset for query (optional)
     *
     * SELECT
     */
    function get_getByFields() {
        try{
            for($i=1; $i<100; $i++){
                $field = $this->getParameter('field'.$i);
                $value = $this->getParameter('value'.$i);
                if($field != '' && $value != ''){
                    $this->model->addCondition($field,$value);
                }
            }
            $this->model->dsql()->calcFoundRows();
            $this->model->setLimit($this->count,$this->offset);
            $data = $this->model->prepareForSelect($this->app->current_user)->getRows();
//            $data = $this->model->getRows();
            $total_rows = $this->model->dsql()->foundRows();

            return [
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
            ];
        }catch(Exception $e){
            return [
                'result'  => 'error',
                'code'    => '5399',
                'message'   => $e->getMessage(),
            ];
        }
    }
    // SELECT
    function get_getById() {
        $id = $this->checkGetParameter('id');
        $_GET['field'] = 'id';
        $_GET['value'] = $id;
        return $this->get_getByField();
    }
    // DELETE
    function get_deleteById() {
        $id = $this->checkGetParameter('id');
        try{
            $this->model->prepareForDelete($this->app->current_user);
            $this->model->delete($id);
            return [
                'result'            => 'success',
                'deleted_record_id' => $id,
            ];
        } catch (Exception_API_CannotDelete $e) {
            return [
                'result'  => 'error',
                'code'    => '5313',
                'message'   => $e->getMessage(),
            ];
        } catch (Exception $e) {
            return [
                'result'  => 'error',
                'code'    => '5399',
                'message' => 'deleteById '.$e->getMessage(),
            ];
        }
    }
    // INSERT, UPDATE
    function post_saveParams(){
        $data_arr = $this->getFancyPost();

        //Check if all required fields are present (if specified)
        try{

            if(isset($this->required_fields) && !empty($data_arr)){
                $not_set = [];
                foreach($this->required_fields as $val){
                    if(!in_array($val,array_keys($data_arr))){
                        $not_set[] = $val;
                    }
                }
                if(count($not_set) > 0){
                    return[
                        'result' => 'error',
                        'message'   => 'Required parameters are not specified :'.implode(',',$not_set),
                    ];
                }
            }
        }catch (Exception $e){
            return [
                'result'  => 'error',
                'code'    => '5399',
                'message' => 'saveParams '.$e->getMessage(),
            ];
        }

        if (is_array($data_arr)) {
            $all = array_merge($_REQUEST,$data_arr);
        } else {
            $all = $_REQUEST;
        }
        $id = $this->getId();
        if ($id) {
            $this->model->tryLoad($id);
            if(!$this->model->loaded()){
                return [
                    'result'  => 'error',
                    'code'    => '5320',
                    'message' => 'Record with the id '.$id.' was not found',
                ];
            }
            try {
                $this->model->prepareForUpdate($this->app->current_user);
            } catch (Exception_API_CannotEdit $e) {
                return [
                    'result'  => 'error',
                    'code'    => '5312',
                    'message' => 'User has ho right to update '.$this->model_class.' with ID='.$id,
                ];
            } catch (Exception $e){
                return [
                    'result'  => 'error',
                    'code'    => '5399',
                    'message' => 'saveParams (update) ' . $e->getMessage(),
                ];
            }
        }else{
            try {
                $this->model->prepareForInsert($this->app->current_user);
            } catch (Exception_API_CannotAdd $e) {
                return [
                    'result'  => 'error',
                    'code'    => '5311',
                    'message' => 'User has ho right to add '.$this->model_class,
                ];
            } catch (Exception $e){
                return [
                    'result'  => 'error',
                    'code'    => '5399',
                    'message' => 'saveParams (insert) ' . $e->getMessage(),
                ];
            }
        }

        try{
            $this->model->set($all);
            $this->model->save();
            return [
                'result' => 'success',
                'data' => $this->model->get(),
            ];
        }catch (Exception $e){
            return [
                'result'  => 'error',
                'code'    => '5399',
                'message' => 'saveParams' . $e->getMessage(),
            ];
        }
    }
    protected function getId(){
        $id = $this->checkGetParameter('id',true);
        if ($id) {
            $this->model->tryLoad($id);
            if(!$this->model->loaded()){
                echo json_encode([//TODO I left it here intentionally, Kostya
                    'result'  => 'error',
                    'code'    => '5320',
                    'message' => 'Record with the id '.$id.' was not found',
                ]);
                exit();
            }
        }
        return $id;
    }
    protected function getFancyPost() {
        try{
            $data = file_get_contents("php://input"); // TODO: not safe data
            $data_arr = json_decode($data,true);
            return $data_arr;
        }catch (Exception $e){
            return [
                'result'=>'error',
                'message'=>$e->getMessage()
            ];
        }
    }




    // just to avoid default actions
    public $allow_list=false;
    public $allow_list_one=false;
    public $allow_add=false;
    public $allow_edit=false;
    public $allow_delete=false;
    function get() {}
    function post($data) {}
    function put($data) {}
    function delete($data) {}
}