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
     * field - which field will be used for search (required)
     * value - value for the field (required)
     * count - count rows (optional)
     * offset - offset for query (optional)
     * method - for defining type of search. If empty - strong search, 'rlike', llike', 'alike'
     */
    function page_getByField() {
        try{
            $field = $this->checkGetParameter('field');
            $value = $this->checkGetParameter('value');
            if($this->method){
                if($this->method == 'rlike') $value = $value . '%';
                if($this->method == 'llike') $value = '%' . $value;
                if($this->method == 'alike') $value = '%' . $value . '%';
                $this->m->addCondition($field,'LIKE',$value);
            }else{
                $this->m->addCondition($field,$value);
            }
            $this->m->setLimit($this->count,$this->offset);
            $data = $this->m->getRows();
            echo json_encode([
                'result' => 'success',
                'data'   => $data,
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
        $id = $this->checkGetParameter('id');
        $this->m->tryLoad($id);
        if(!$this->m->loaded()){
            echo json_encode([
                'result' => 'error',
                'error_message' => 'Record with the id was not found',
            ]);
            exit();
        }
        $this->m->set($_REQUEST);
        $this->m->save();
        echo json_encode([
            'result' => 'success',
            'data' => $this->m->get(),
        ]);
        exit;
    }

}