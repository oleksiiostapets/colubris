<?php
class Grid_Quotes extends Grid_Advanced {
    function init() {
        parent::init();
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->removeColumn('user');
        $this->removeColumn('name');
    }
    function format_status($field){
    	switch($this->current_row[$field]){
    		case 'Quotation Requested':
    			$this->current_row_html[$field] = 'Quotation requested';
    			$this->row_t->setHTML('painted','quotation_requested');
    			break;
    		case 'Estimate Needed':
    			$this->current_row_html[$field] = 'Estimate needed';
    			$this->row_t->setHTML('painted','estimate_needed');
    			break;
   			case 'Not Estimated':
   				$this->current_row_html[$field] = 'Not estimated';
    			$this->row_t->setHTML('painted','not_estimated');
   				break;
			case 'Estimated':
				$this->current_row_html[$field] = 'Estimated';
    			$this->row_t->setHTML('painted','estimated');
				break;
			case 'Estimation Approved':
				$this->current_row_html[$field] = 'Estimation approved';
    			$this->row_t->setHTML('painted','estimation_approved');
				break;
			case 'Finished':
				$this->current_row_html[$field] = 'Finished';
    			$this->row_t->setHTML('painted','finished');
				break;
    						
    		default:
    			$this->current_row_html[$field] = $this->current_row[$field];
    			$this->row_t->setHTML('painted','');
    			break;
    	}
    }
    function format_durdead($field){
    	echo $this->current_row['duration'];
    }
    function formatRow() {
    	parent::formatRow();
    	
    	//$this->js('click')->_selector('[data-id='.$this->current_row['id'].']')->univ()->redirect($this->current_row['id']);
    	
    	$this->current_row_html['requirements']=($this->current_row['status']=='Quotation Requested')?$this->current_row_html['requirements']:'';
    	$this->current_row_html['approve']=($this->current_row['status']=='Estimated')?$this->current_row_html['approve']:'';
    	$this->current_row_html['estimation']=($this->current_row['status']=='Quotation Requested')?$this->current_row_html['estimation']:'';
    	$this->current_row_html['send_to_client']=($this->current_row['status']=='Estimated')?$this->current_row_html['send_to_client']:'';
    	$this->current_row_html['estimate']=($this->current_row['status']=='Estimate Needed')?$this->current_row_html['estimate']:'';
    	if ($this->api->auth->model['is_client']){
	    	if ( ($this->current_row['status']=='Not Estimated') || ($this->current_row['status']=='Quotation Requested') ){
	    		$this->current_row_html['edit']="<button type=\"button\" class=\"button_edit\" onclick=\"$(this).univ().ajaxec('/public/?page=client/quotes&edit=".$this->current_row['id']."&colubris_client_quotes_client_quotes_grid_quotes_edit=".$this->current_row['id']."')\">Edit</button>";
	    	}else{
	    		$this->current_row_html['edit']="";
	    	}
    	}

        $this->current_row_html['project'] =
                '<div class="quote_name">'.$this->current_row['name'].'</div>'.
                '<div class="quote_project">Project:<span>'.$this->current_row['project'].'</span></div>'.
                '<div class="quote_client">User:<span>'.$this->current_row['user'].'</span></div>'
        ;
    }
    function defaultTemplate() {
    	return array('grid/colored');
    }
    
    function precacheTemplate() {
    	$this->row_t->trySetHTML('painted', '<?$painted?>');
    	parent::precacheTemplate();
    }
}