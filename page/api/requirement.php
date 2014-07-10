<?php
/**
 * Created by Vadym Radvansky
 * Date: 7/8/14 12:12 PM
 */
class page_api_requirement extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Requirement');
    }

    function page_getForQuote() {
        $quote_id = $this->getQuoteId();
        $data = $this->m->addCondition('quote_id',$quote_id)->getRows();
        echo json_encode([
            'result' => 'success',
            'data'   => $data,
        ]);
        exit();

    }
    function page_saveAll() {
        $quote_id = $this->getQuoteId();
        echo $quote_id;
        exit();
    }
    private function getQuoteId() {
        $quote_id = $this->checkGetParameter('quote_id'); // method from trait
        return $quote_id;
    }
}