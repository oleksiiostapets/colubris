<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/14/13
 * Time: 12:34 PM
 * To change this template use File | Settings | File Templates.
 */
class Grid_Attachments extends Grid {

    function init() {
        parent::init();
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->removeColumn('file');
        $this->removeColumn('file_thumb');
        $this->removeColumn('updated_dts');
    }
    function formatRow() {
        if ($this->current_row['file_thumb'] != '') {
            $file = '<a target="_blank" href="'. $this->current_row['file'] .'"><img width="50" src="'.$this->current_row['file_thumb'].'"></a>';
        } else {
            $file = '<a target="_blank" href="'. $this->current_row['file'] .'">download</a>';
        }

        $this->current_row_html['description'] =
                $this->current_row['description'].'<br>'.
                '<div class="timestamp">'.$this->current_row['updated_dts'].'</div>'.
                $file
        ;
        parent::formatRow();
    }
}