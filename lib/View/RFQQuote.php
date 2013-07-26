<?php
class View_RFQQuote extends View {
    function init(){
        parent::init();

        $this->add('H4')->set('Quote:');
        $this->add('P')->set('Project - '.$this->quote->get('project'));
        $this->add('P')->set('User - '.$this->quote->get('user'));
        $this->add('P')->set('Name - '.$this->quote->get('name'));
        $this->add('P')->set('Estimated - '.$this->quote->get('estimated'));
        $this->add('P')->set('General requirement - '.$this->quote->get('general'));
        
        $v=$this->add('View')->setClass('left');
        $v->add('H4')->set('Requirements:');
        
        $v=$this->add('View')->setClass('right');
        $this->quote->get('estimated')>0?$estimate=$this->quote->get('estimated'):$estimate=0;
        $v->add('View')->setClass('red_color')->set('Estimated: '.$estimate.'hours');
        
        $v=$this->add('View')->setClass('clear');
        
    }
}
