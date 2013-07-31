<?php
class Grid_Reqcomments extends Grid {
    function format_download($field){
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
	function format_text($field){
       	$this->current_row_html[$field] = '<span style="white-space:wrap;">'.$this->current_row[$field].'</span>';
       	$this->tdparam[$this->getCurrentIndex()][$field]['style']='white-space: wrap';
    }
    function formatRow() {
    	parent::formatRow();
    	
    	if($this->current_row['user_id']==$this->api->auth->model['id']){
    		$this->current_row_html['edit']='<button type="button" class="pb_edit ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">Edit</button>';
    		$this->current_row_html['delete']='<button type="button" class="red button_delete ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" onclick="$(this).univ().confirm(\'Are you sure?\').ajaxec(\''.$this->api->url(null).'/comments&amp;requirement_id='.$this->current_row['requirement_id'].'&amp;delete='.$this->current_row['id'].'&amp;1__id_reqcomments_delete='.$this->current_row['_id'].'\')" role="button" aria-disabled="false">Delete</button>';
    	}else{
    		$this->current_row_html['edit']="";
    		$this->current_row_html['delete']="";
    	}
    }
}