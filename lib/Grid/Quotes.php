<?php
class Grid_Quotes extends Grid_Advanced {
    function format_status($field){
    	switch($this->current_row[$field]){
    		case 'quotation_requested':
    			$this->current_row_html[$field] = 'Quotation requested';
    			$this->row_t->setHTML('odd_even','quotation_requested');
    			break;
    		case 'estimate_needed':
    			$this->current_row_html[$field] = 'Estimate needed';
    			$this->row_t->setHTML('odd_even','estimate_needed');
    			break;
   			case 'not_estimated':
   				$this->current_row_html[$field] = 'Not estimated';
    			$this->row_t->setHTML('odd_even','not_estimated');
   				break;
			case 'estimated':
				$this->current_row_html[$field] = 'Estimated';
    			$this->row_t->setHTML('odd_even','estimated');
				break;
			case 'estimation_approved':
				$this->current_row_html[$field] = 'Estimation approved';
    			$this->row_t->setHTML('odd_even','estimation_approved');
				break;
			case 'finished':
				$this->current_row_html[$field] = 'Finished';
    			$this->row_t->setHTML('odd_even','finished');
				break;
    						
    		default:
    			$this->current_row_html[$field] = $this->current_row[$field];
    			$this->row_t->setHTML('odd_even','');
    			break;
    	}
    }
    function formatRow() {
    	parent::formatRow();
    	
    	$this->current_row_html['edit']=($this->current_row['status']=='quotation_requested')?$this->current_row_html['edit']:'';
    	$this->current_row_html['approve']=($this->current_row['status']=='estimated')?$this->current_row_html['approve']:'';
    	$this->current_row_html['estimation']=($this->current_row['status']=='quotation_requested')?$this->current_row_html['estimation']:'';
    	$this->current_row_html['send_to_client']=($this->current_row['status']=='estimated')?$this->current_row_html['send_to_client']:'';
    	$this->current_row_html['estimate']=($this->current_row['status']=='estimate_needed')?$this->current_row_html['estimate']:'';
    }
}