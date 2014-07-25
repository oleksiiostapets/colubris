<?php
class Page_api_general extends Page {

    use Helper_Url;

    protected $m;
    protected $count;
    protected $offset;
    protected $method;

    function init() {
        parent::init();

        $this->getParameter('count') ? $this->count = $this->getParameter('count') : $this->count = 999999999999;
        $this->getParameter('offset') ? $this->offset = $this->getParameter('offset') : $this->offset = 0;
        $this->getParameter('method') ? $this->method = $this->getParameter('method') : $this->method = false;
    }

    /**
     * Parameters:
     * field - which field will be used for search (optional)
     * value - value for the field (optional)
     * count - count rows (optional)
     * offset - offset for query (optional)
     * method - for defining type of search. If empty - strong search, 'rlike', llike', 'alike'
     */
    function page_getByField() {
        try{
            $field = $this->getParameter('field');
            $value = $this->getParameter('value');
            if($field != ''){
                if($this->method){
                    if($this->method == 'rlike') $value = $value . '%';
                    if($this->method == 'llike') $value = '%' . $value;
                    if($this->method == 'alike') $value = '%' . $value . '%';
                    $this->m->addCondition($field,'LIKE',$value);
                }else{
                    $this->m->addCondition($field,$value);
                }
            }
            $this->m->setLimit($this->count,$this->offset);
            $data = $this->m->getRows();
            $this->m->setLimit(999999999,0);
            $total_rows = count($this->m->getRows());
//            var_dump($data);exit;
            echo json_encode([
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
            ]);
            exit();
        }catch(Exception $e){
            echo json_encode([
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ]);
            exit();
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
     */
    function page_getByFields() {
        try{
            $field1 = $this->getParameter('field1');
            $value1 = $this->getParameter('value1');
            if($field1 != '' && $value1 != ''){
                $this->m->addCondition($field1,$value1);
            }
            $field2 = $this->getParameter('field2');
            $value2 = $this->getParameter('value2');
            if($field2 != '' && $value2 != ''){
                $this->m->addCondition($field2,$value2);
            }
            $field3 = $this->getParameter('field3');
            $value3 = $this->getParameter('value3');
            if($field3 != '' && $value3 != ''){
                $this->m->addCondition($field3,$value3);
            }
            $field4 = $this->getParameter('field4');
            $value4 = $this->getParameter('value4');
            if($field4 != '' && $value4 != ''){
                $this->m->addCondition($field4,$value4);
            }
            $field5 = $this->getParameter('field5');
            $value5 = $this->getParameter('value5');
            if($field5 != '' && $value5 != ''){
                $this->m->addCondition($field5,$value5);
            }
            $this->m->setLimit($this->count,$this->offset);
            $data = $this->m->getRows();
            $this->m->setLimit(999999999,0);
            $total_rows = count($this->m->getRows());

            echo json_encode([
                'result' => 'success',
                'data'   => $data,
                'total_rows' => $total_rows,
            ]);
            exit();
        }catch(Exception $e){
            echo json_encode([
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ]);
            exit();
        }
    }
    function page_getById() {
        $id = $this->checkGetParameter('id');
        $_GET['field'] = 'id';
        $_GET['value'] = $id;
        $this->page_getByField();
    }
    function page_saveParams(){
        $data = file_get_contents("php://input");
        $data_arr = @json_decode($data,true);
        if (is_array($data_arr)) {
            $all = array_merge($_REQUEST,$data_arr);
        } else {
            $all = $_REQUEST;
        }
        $id = $this->checkGetParameter('id');
        $this->m->tryLoad($id);
        if(!$this->m->loaded()){
            echo json_encode([
                'result' => 'error',
                'error_message' => 'Record with the id '.$id.' was not found',
            ]);
            exit();
        }
        $this->m->set($all);
        $this->m->save();
        echo json_encode([
            'result' => 'success',
            'data' => $this->m->get(),
        ]);
        exit;
    }

}