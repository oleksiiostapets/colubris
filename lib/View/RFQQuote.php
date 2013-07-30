<?php
class View_RFQQuote extends View {
    function init(){
        parent::init();

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
        	if(  ($this->quote->get('status')=='quotation_requested')
        			|| ( ($this->api->auth->model['is_client']) && ($this->quote->get('status')=='not_estimated') )  
        			){
        }
	        $b=$v->add('Button')->set('Edit requirements');
	        $b->js('click')->univ()->redirect($this->api->url('/'.$page[0].'/quotes/rfq/step2',array('quote_id'=>$this->quote->get('id'))));
        }
        
        $v=$this->add('View')->setClass('clear');
        
        $v=$this->add('View')->setClass('left');
        $v->add('H4')->set('Requirements:');
        
        $v=$this->add('View')->setClass('right');
        $this->quote->get('estimated')>0?$estimate=$this->quote->get('estimated'):$estimate=0;
        $v->add('View')->setClass('red_color')->set('Estimated: '.$estimate.'hours');
        
        $v=$this->add('View')->setClass('clear');
        
    }
}
