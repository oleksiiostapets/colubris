<?php
class Grid_Logs extends Grid {
    function format_data($field){
        // Getting info from "changed_fields" field
        $dc=json_decode($this->current_row['changed_fields']);

        // Data of head field
        $d=json_decode($this->current_row[$field]);
        $new_val='<table class="logs">';
        foreach($d as $k=>$v){
            // Checking if field was changed
            $style="";
            foreach($dc as $kc=>$vc){
                if($kc==$k) $style='font-weight:bold;';
            }

            $new_val.='<tr style="'.$style.'"><td>'.$k.'</td><td style="white-space:wrap;">'.$v.'</td></tr>';
        }
        $new_val.='</table>';

        $this->current_row_html[$field] = $new_val;//json_decode($this->current_row[$field]);
    }
    function format_changed_fields($field){
        $d=json_decode($this->current_row[$field]);
        $new_val='<table class="logs">';
        foreach($d as $k=>$v){
            $new_val.='<tr><td>'.$k.'</td></tr>';
        }
        $new_val.='</table>';

        $this->current_row_html[$field] = $new_val;//json_decode($this->current_row[$field]);
    }
}
