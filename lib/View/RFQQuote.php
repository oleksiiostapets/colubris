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

        $v=$this->add('View')->setClass('left');
        
        $v->add('H4')->set('Quote:');
        $v->add('P')->set('Project - '.$this->quote->get('project'));
        $v->add('P')->set('User - '.$this->quote->get('user'));
        $v->add('P')->set('Name - '.$this->quote->get('name'));
        $v->add('P')->set('Estimated - '.$this->quote->get('estimated'));
        $v->add('P')->set('General requirement - '.$this->quote->get('general'));
        
        $v=$this->add('View')->setClass('right');
        $page=explode('_',$this->api->page);
        if($page[count($page)-1]!='step2'){
        	if( !($this->api->auth->model['is_developer']) &&
                ($this->quote->get('status')=='quotation_requested'
        			|| ( $this->api->auth->model['is_client'] && $this->quote->get('status')=='not_estimated' ))
            ){
		        $b=$v->add('Button')->set('Edit requirements');
		        $b->js('click')->univ()->redirect($this->api->url('/'.$page[0].'/quotes/rfq/step2',array('quote_id'=>$this->quote->get('id'))));
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
}
