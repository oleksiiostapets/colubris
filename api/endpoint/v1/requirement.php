<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:12 PM
 */
class endpoint_v1_requirement extends Endpoint_v1_General {

    public $model_class = 'Requirement';
    protected $required_fields = ['name','quote_id','user_id'];

    function init() {
        parent::init();
    }

    function get_getForQuote() {
        $quote_id = $this->getQuoteId();
        $this->model->prepareForSelect($this->app->current_user);
        $this->model->addCondition('quote_id',$quote_id)->addCondition('is_deleted',false);
        try{
            $data = $this->model->getRows();
            return[
                'result' => 'success',
                'data'   => $data,
            ];
        }catch (Exception $e){
            return[
                'result' => 'error',
                'error_message'   => $e->getMessage(),
            ];
        }


    }
    function get_saveAll() {//TODO WTF???
        $quote_id = $this->getQuoteId();
        return $quote_id;
    }
    private function getQuoteId() {
        $quote_id = $this->checkGetParameter('quote_id'); // method from trait
        return $quote_id;
    }
}