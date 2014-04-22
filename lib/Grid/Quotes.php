<?php
class Grid_Quotes extends Grid {
    public $allowed_actions = array();
    public $posible_actions = array(
        'requirements'   => array('status'=>array('Quotation Requested'),'name'=>'Requirements',            'get_var'=>'requirements'),
        'estimation'     => array('status'=>array('Quotation Requested'),'name'=>'Submit for Quotation',    'get_var'=>'estimation'),
        'send_to_client' => array('status'=>array('Estimated'),          'name'=>'Send Quote to the client','get_var'=>'send_to_client'),
        'approve'        => array('status'=>array('Estimated'),          'name'=>'Approve Estimation',      'get_var'=>'approve'),
        'estimate'       => array('status'=>array('Estimate Needed'),    'name'=>'Estimate',                'get_var'=>'estimate'),
        'details'        => array('status'=>array('any'),                'name'=>'Details',                 'get_var'=>'details'),
        'active'         => array('status'=>array('any'),                'name'=>'Move to Archive',         'get_var'=>'in_archive'),
        'archive'        => array('status'=>array('any'),                'name'=>'Extract from Archive',    'get_var'=>'activate'),
        'edit_details'   => array('status'=>array('Not Estimated','Quotation Requested'),
                                                                         'name'=>'Edit Details',            'get_var'=>'edit_details'),
    );
    function init() {
        parent::init();

        $this->addClass('zebra bordered');

        if (!count($this->allowed_actions) && isset($this->owner->allowed_actions)) {
            $this->allowed_actions = $this->owner->allowed_actions;
        } else if ( !count($this->allowed_actions) ) {
            throw $this->exception('What actions are allowed?');
        }


        /* ********************************
         *
         *     perform action if allowed
         *
         */

        // estimate
        if( $_GET['estimate'] ) {
            if ( in_array('estimate',$this->allowed_actions) ) {
                $this->js()->univ()->redirect($this->api->url('quotes/rfq/requirements',
               			array('quote_id'=>$_GET['estimate'])))->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['estimate']['name'].'" is not allowed!')->execute();
            }
        }

        // requirements
        if( $_GET['requirements'] ) {
            if ( in_array('requirements',$this->allowed_actions) ) {
                $this->js()->univ()->redirect($this->api->url('quotes/rfq/requirements',
               			array('quote_id'=>$_GET['requirements'])))->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['requirements']['name'].'" is not allowed')->execute();
            }
        }

        // estimation
        if( $_GET['estimation'] ){
            if ( in_array('estimation',$this->allowed_actions) ) {
                $quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($_GET['estimation']);
               	$quote->set('status','estimate_needed');
               	$quote->save();
                $this->js()->reload()->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['estimation']['name'].'" is not allowed')->execute();
            }
        }

        // approve
        if( $_GET['approve'] ){
            if ( in_array('approve',$this->allowed_actions) ) {
                $quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($_GET['approve']);
                $quote->approve();

                // Sending email to client
                $this->api->mailer->addClientReceiver($quote->get('project_id'));
                $this->api->mailer->sendMail('quote_approved',array(
                    'quotename'=>$quote->get('name'),
                    'link'=>$this->api->siteURL().$this->api->url('quotes/rfq/requirements',array('quote_id'=>$quote->get('id'))),
                ));

                // Clearing email receivers and Sending email to managers
                $this->api->mailer->receivers=array();
                $this->api->mailer->addAllManagersReceivers($this->api->auth->model['organisation_id']);
                $this->api->mailer->sendMail('quote_approved',array(
                    'quotename'=>$quote->get('name'),
                    'link'=>$this->api->siteURL().$this->api->url('quotes/rfq/requirements',array('quote_id'=>$quote->get('id'))),
                ));

                $this->js()->reload()->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['approve']['name'].'" is not allowed')->execute();
            }
        }

        // send_to_client
        if( $_GET['send_to_client'] ){
            if ( in_array('send_to_client',$this->allowed_actions) ) {
                try {
                    $client = $this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($_GET['send_to_client'])->sendEmailToClient();
                } catch (Exception_QuoteHasNoClient $e) {
                    $this->js()->univ()->errorMessage('The project of this quote has no client!')->execute();
                } catch (Exception_ClientHasNoEmail $e) {
                    $this->js()->univ()->errorMessage('Error! The client '.$e->more_info['name'].' has no email. Please add email for the client.')->execute();
                }
                $this->js()->univ()->successMessage('Mail sent to '.$client['email'])->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['send_to_client']['name'].'" is not allowed')->execute();
            }
        }

        // details
        if( $_GET['details'] ){
            if ( in_array('details',$this->allowed_actions) ) {
                $this->js()->univ()->redirect($this->api->url('quotes/rfq/requirements',
                        array('quote_id'=>$_GET['details'])))->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['details']['name'].'" is not allowed')->execute();
            }
        }

        // edit_details
        if( $_GET['edit_details'] ){
            if ( in_array('edit_details',$this->allowed_actions) ) {
            $this->js()->univ()->redirect($this->api->url('quotes/rfq/requirements',
                array('quote_id'=>$_GET['edit_details'])))->execute();
            } else {
                $this->js()->univ()->errorMessage('Action "'.$this->posible_actions['edit_details']['name'].'" is not allowed')->execute();
            }
        }

    }
    function setModel($model, $actual_fields = UNDEFINED) {
        $this->addColumn('id');
        $this->addColumn('quotation');
        $this->addColumn('estimate_info');

        parent::setModel($model, $actual_fields);

        // remove columns
        $this->removeColumn('project');
        $this->removeColumn('user');
        $this->removeColumn('name');
        $this->removeColumn('estimpay');
        $this->removeColumn('rate');
        $this->removeColumn('currency');
        $this->removeColumn('spent_time');
        $this->removeColumn('estimated');
        if ($this->api->currentUser()->isClient()){
            $this->removeColumn('show_time_to_client');
        }
        // add columns after model columns
        $this->addColumn('actions');

        // formatters
        $this->addFormatter('status','wrap');
        $this->addFormatter('status','status');

        $this->addPaginator(25);
    }
    function formatRow() {
    	parent::formatRow();
    	//$this->js('click')->_selector('[data-id='.$this->current_row['id'].']')->univ()->redirect($this->current_row['id']);

        $this->current_row_html['quotation'] =
                '<div class="quote_name"><a href="'.$this->api->url('quotes/rfq/requirements',array(
                    'quote_id'=>$this->current_row['id']
                )).'">'.$this->current_row['name'].'</a></div>'.
                '<div class="quote_project"><span>Project:</span>'.$this->current_row['project'].'</div>'
        ;
        if (!$this->api->currentUser()->isClient()){
            $this->current_row_html['quotation'] .=
                    '<div class="quote_client"><span>User:</span>'.$this->current_row['user'].'</div>'
            ;
        }
        // estimated time
        if ($this->current_row['estimated'] == '') {
            $this->current_row['estimated'] = '-';
        } else {
            $this->current_row['estimated'] .= ' hours';
        }

        // spent_time
        if ($this->current_row['spent_time'] == '') {
            $this->current_row['spent_time'] = '-';
        } else {
            $this->current_row['spent_time'] .= ' hours';
        }

        // rate
        if ($this->current_row['rate'] != '') {
            $this->current_row['rate'] = $this->current_row['rate'].' '.$this->current_row['currency'];
        } else {
            $this->current_row['rate'] = '-';
        }

        // warranty_end
        if ($this->current_row['warranty_end'] != '-') {
            $this->current_row['warranty_end'] = date("Y-m-d",time($this->current_row['warranty_end']));
        } else {
            $this->current_row['warranty_end'] = '-';
        }

        // estimpay
        if ($this->current_row['estimpay'] != '') {
            $this->current_row['estimpay'] = $this->current_row['estimpay'].' '.$this->current_row['currency'];
        } else {
            $this->current_row['estimpay'] = '-';
        }

        if ($this->api->currentUser()->isClient() && !$this->current_row['show_time_to_client']){
            $this->current_row_html['estimate_info'] =
                    '<div class="quote_estimpay"><span>Est.pay:</span>'.$this->current_row['estimpay'].'</div>'
            ;
        }elseif($this->api->currentUser()->canSeeFinance()){
            $this->current_row_html['estimate_info'] =
                '<div class="quote_estimated"><span>Est.time:</span>'.$this->current_row['estimated'].'</div>'.
                '<div class="quote_rate"><span>Rate:</span>'.$this->current_row['rate'].'</div>'.
                '<div class="quote_estimpay"><span>Est.pay:</span>'.$this->current_row['estimpay'].'</div>'.
                '<div class="quote_spent_time"><span>Spent:</span>'.$this->current_row['spent_time'].'</div>'
            ;
        }else{
            $this->current_row_html['estimate_info'] =
                '<div class="quote_estimated"><span>Est.time:</span>'.$this->current_row['estimated'].'</div>'.
                '<div class="quote_spent_time"><span>Spent:</span>'.$this->current_row['spent_time'].'</div>'
            ;
        }

        // actions
        $v = $this->add('View','action_'.$this->current_id,'content');
        foreach ($this->allowed_actions as $action) {
            if (
                in_array($this->current_row['status'], $this->posible_actions[$action]['status']) ||
                in_array('any', $this->posible_actions[$action]['status'])
            ) {
                $v->add('View')->set($this->posible_actions[$action]['name'])->addClass('a_look')
                        ->js('click')->univ()->ajaxec($this->api->url(null,array($this->posible_actions[$action]['get_var']=>$this->current_id)));
            }
        }
        $this->current_row_html['actions'] = $v->getHTML();
    }
    function format_status($field){
    	switch($this->current_row[$field]){
    		case 'Quotation Requested':
    			$this->current_row_html[$field] = 'Quotation requested';
    			// $this->row_t->set('painted','quotation_requested');
    			break;
    		case 'Estimate Needed':
    			$this->current_row_html[$field] = 'Estimate needed';
    			// $this->row_t->set('painted','estimate_needed');
    			break;
   			case 'Not Estimated':
   				$this->current_row_html[$field] = 'Not estimated';
    			// $this->row_t->set('painted','not_estimated');
   				break;
			case 'Estimated':
				$this->current_row_html[$field] = 'Estimated';
    			// $this->row_t->set('painted','estimated');
				break;
			case 'Estimation Approved':
				$this->current_row_html[$field] = 'Estimation approved';
    			// $this->row_t->set('painted','estimation_approved');
				break;
			case 'Finished':
				$this->current_row_html[$field] = 'Finished';
    			// $this->row_t->set('painted','finished');
				break;
    						
    		default:
    			$this->current_row_html[$field] = $this->current_row[$field];
    			// $this->row_t->set('painted','');
    			break;
    	}
    }
    function format_durdead($field){
    	echo $this->current_row['duration'];
    }

//    function defaultTemplate() {
//    	return array('grid/colored');
//    }
   
    function precacheTemplate() {
    	$this->row_t->trySetHTML('painted', '{$painted}');
    	parent::precacheTemplate();
    }
}
