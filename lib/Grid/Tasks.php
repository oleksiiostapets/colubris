<?php
class Grid_Tasks extends Grid_Advanced {
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
    
    function precacheTemplate()
    {
    	$this->row_t->trySetHTML('painted', '<?$painted?>');
    	parent::precacheTemplate();
    }
}