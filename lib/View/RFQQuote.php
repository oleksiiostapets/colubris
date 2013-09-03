<?php
class View_RFQQuote extends View {
    public $quote;
    public $total_view;
    function init(){
        parent::init();

        // $this->quote must be setted
        if (is_null($this->quote)) {
            throw $this->exception('Set $this->quote while adding.');
        }

        $v=$this->add('View');//->setClass('left span6');

        // quote description
        $v->add('H4')->set('Quote:');
        $fields_required = array('project','user','name',/*'estimated',*/'general_description',);
        $this->addQuoteInfoGrid($v, $fields_required);
        
        //$v=$this->add('View')->setClass('right');
        $page=explode('_',$this->api->page);
        if($page[count($page)-1]!='requirements'){
        	if( !($this->api->auth->model['is_developer']) &&
                ($this->quote->get('status')=='quotation_requested'
        			|| ( $this->api->auth->model['is_client'] && $this->quote->get('status')=='not_estimated' ))
            ){
		        $b=$v->add('Button')->set('Edit requirements');
		        $b->js('click')->univ()->redirect($this->api->url('/'.$page[0].'/quotes/rfq/requirements',array('quote_id'=>$this->quote->get('id'))));
            }
        }
        
        $v=$this->add('View')->setClass('clear');
        
        $v=$this->add('View')->setClass('left');
        $v->add('H4')->set('Requirements:');
        
        $v=$this->add('View')->setClass('floating_total radius_10');
        $v->js(true)->colubris()->floating_total($v->name);
        $this->quote->get('estimated')>0?$estimate=$this->quote->get('estimated'):$estimate=0;
        $this->total_view = $v->add('View')
                ->setClass('estimate_total_time_to_reload')
                ->set('Estimated: '.$estimate.'hours');
        $this->total_view->js('reload')->reload();

        $v=$this->add('View')->setClass('clear');
        
    }


    function addQuoteInfoGrid($v,$fields_required) {
        $count = 0;
        $source = array();
        foreach ($this->quote->get() as $key=>$value) {
            if (in_array($key,$fields_required)) {
                $source[$count]['name'] = ucwords($key);
                $source[$count]['value'] = $value;
                $count++;
            }
        }
        $gr = $v->add('Grid');
        $gr->addClass('zebra bordered');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','');
        $gr->setSource($source);

    }
}
