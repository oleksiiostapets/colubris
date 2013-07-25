<?php
class Grid extends Grid_Advanced {
    function format_download($field){//var_dump($this->current_row['migrated_image']);
    	if ($this->current_row[$field]!=""){
    		$name=$this->current_row[$field];
    		$arr_name=explode('.',$name);
    		$image_exts=array(
    				'jpg',
    				'jpeg',
    				'png',
    				'gif',
    				'bmp',
    				);
    		if(in_array($arr_name[count($arr_name)-1],$image_exts)){
	        	$this->current_row_html[$field] =
    		        	'<a target="_blank" href="'. $this->current_row[$field] .'"><img width="150" src="'.$this->current_row[$field].'"></a>';
    		}else{
	        	$this->current_row_html[$field] =
    		        	'<a target="_blank" href="'. $this->current_row[$field] .'">download</a>';
    		}
    	}else{
    		$this->current_row_html[$field] = '';
    	}
    }
}