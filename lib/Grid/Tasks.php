<?php
class Grid_Tasks extends Grid_Advanced {
    function format_status($field){
    	switch($this->current_row[$field]){
    		case 'started':
    			$this->row_t->setHTML('odd_even','started');
    			break;
    		case 'finished':
    			$this->row_t->setHTML('odd_even','unstarted');
    			break;
   			case 'rejected':
    			$this->row_t->setHTML('odd_even','rejected');
   				break;
			case 'accepted':
    			$this->row_t->setHTML('odd_even','accepted');
				break;
    						
    		default:
    			$this->current_row_html[$field] = $this->current_row[$field];
    			$this->row_t->setHTML('odd_even','');
    			break;
    	}
    }
}