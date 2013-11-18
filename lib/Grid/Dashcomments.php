<?php
class Grid_Dashcomments extends Grid {
    function init() {
        parent::init();
        $this->addClass('zebra bordered');
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->getColumn('project_name')->setCaption('Info');
        $this->removeColumn('user');
        $this->removeColumn('file');
        $this->removeColumn('file_thumb');
        $this->removeColumn('created_dts');
        $this->removeColumn('quote_id');
        $this->removeColumn('requirement_id');
        $this->removeColumn('task_id');
        $this->removeColumn('task_name');
        $this->removeColumn('quote_name');
        $this->removeColumn('quote_status');
        $this->removeColumn('requirement_name');
    }
    function formatRow() { // var_dump($this->current_row); echo '<hr>';

        // thumb or download link
        if ($this->current_row['file_thumb'] != '') {
            $file = '<a target="_blank" href="'. $this->current_row['file'] .'"><img width="50" src="'.$this->current_row['file_thumb'].'"></a>';
        } else {
            if ($this->current_row['file'] != '') {
                $file = '<a target="_blank" href="'. $this->current_row['file'] .'">download</a>';
            } else {
                $file = '';
            }
        }

        // all fields in one field
        $this->current_row_html['text'] =
                '<strong>'.$this->current_row['user'].':</strong><br>'.
                '<div class="timestamp">'.$this->current_row['created_dts'].'</div>'.
                '<div class="comment radius_10">'.nl2br($this->current_row['text']).'</div>'.
                $file
        ;

        parent::formatRow();

        if ($this->current_row['quote_name']){
            $this->current_row_html['project_name'] =
                '<table>'.
                    '<tr>'.
                        '<td style="font-weight:bold;">Project:&nbsp;</td>'.
                        '<td>'.$this->current_row['project_name'].'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td style="font-weight:bold;">Quote:&nbsp;</td>'.
                        '<td>'.'<a href="'.$this->api->url('/quotes/rfq/requirements',array('quote_id'=>$this->current_row['quote_id'])).'">'.$this->current_row['quote_name'].'</a>'.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td style="font-weight:bold;">Requirement:&nbsp;</td>'.
                        '<td>'.'<a href="'.$this->api->url('/quotes/rfq/requirement',array('requirement_id'=>$this->current_row['requirement_id'])).'">'.$this->current_row['requirement_name'].'</a>'.'</td>'.
//                        '<td>'.'<a href="'.$this->api->url('/quotes/rfq/requirements',array('quote_id'=>$this->current_row['quote_id'])).'#'.$this->api->name.'_quotes_rfq_requirements_crud_grid_requirements_more_'.$this->current_row['requirement_id'].'">'.$this->current_row['requirement_name'].'</a>'.'</td>'.
                    '</tr>'.
                '</table>'
            ;
        }else{
            $this->current_row_html['project_name'] =
                '<table>'.
                    '<tr>'.
                        '<td style="font-weight:bold;">Project:&nbsp;</td>'.
                        '<td>'.$this->current_row['project_name'].'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td style="font-weight:bold;">Task:&nbsp;</td>'.
                        '<td>'.'<a href="'.$this->api->url('/task',array('task_id'=>$this->current_row['task_id'])).'">'.$this->current_row['task_name'].'</a>'.'</td>'.
                    '</tr>'.
                    '</table>'
            ;
        }


        // edit and delete buttons
    	if($this->current_row['user_id']!=$this->api->auth->model['id']){
    		$this->current_row_html['edit']="";
    		$this->current_row_html['delete']="";
    	}
    }
    function format_text($field){
        $this->current_row_html[$field]=nl2br($this->current_row[$field]);
    }
}