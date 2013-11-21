<?php
class Grid_Quote extends Grid {
    function init() {
        parent::init();

        $this->addClass('zebra bordered');

    }
    function formatRow() {
    	parent::formatRow();

        $this->current_row_html['general_description']=nl2br($this->current_row_html['general_description']);
    }
    function format_download($field){
        if(strpos($this->current_row[$field],'upload/')===false){
            $this->current_row_html[$field]=$this->current_row[$field];
        }else{
            if ($this->current_row[$field]!=''){
                $this->current_row_html[$field]='<a target="_blank" href="'.$this->current_row[$field].'"/>file</a>';
            }else{
                $this->current_row_html[$field]='';
            }
        }
    }
}
