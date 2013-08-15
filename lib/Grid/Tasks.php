<?php
class Grid_Tasks extends Grid_Advanced {
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->removeColumn('estimate');
    }
    function formatRow() {
        parent::formatRow();
        if ($this->current_row['spent_time'] == '') {
            $this->current_row['spent_time'] = '0.00';
        }
        $this->current_row_html['spent_time'] =
                '<div style="white-space:nowrap;" class="spent_time">'.$this->current_row['spent_time'].'</div>'.
                '<div style="white-space:nowrap;" class="estimate">Est: ('.$this->current_row['estimate'].')</div>'
        ;
    }
    function format_status($field){
    	switch($this->current_row[$field]){
    		case 'started':
    			$this->row_t->setHTML('painted','started');
    			break;
    		case 'finished':
    			$this->row_t->setHTML('painted','finished');
    			break;
            case 'tested':
                $this->row_t->setHTML('painted','tested');
                break;
   			case 'rejected':
    			$this->row_t->setHTML('painted','rejected');
   				break;
			case 'accepted':
    			$this->row_t->setHTML('painted','accepted');
				break;
    						
    		default:
    			$this->row_t->setHTML('painted','');
    			break;
    	}
    }
    function format_text($field){
    	$this->current_row_html[$field] = '<span style="white-space:wrap;">'.$this->current_row[$field].'</span>';
    	$this->tdparam[$this->getCurrentIndex()][$field]['style']='white-space: wrap';
    }
    function defaultTemplate() {
    	return array('grid/colored');
    }
    
    function precacheTemplate() {
    	$this->row_t->trySetHTML('painted', '<?$painted?>');
    	parent::precacheTemplate();
    }
}