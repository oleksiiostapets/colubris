<?php
class Grid extends Grid_Advanced {
    function format_download($field){//var_dump($this->current_row['migrated_image']);
    	if ($this->current_row[$field]!=""){
        	$this->current_row_html[$field] =
            	'<a target="_blank" href="'. $this->current_row[$field] .'">download</a>';
    	}else{
    		$this->current_row_html[$field] = '';
    	}
    }
}