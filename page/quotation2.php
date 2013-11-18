<?php
class page_quotation2 extends Page {
    function page_index(){

        if ($this->api->recall('guest_quote_id')=='') $this->js()->univ()->redirect($this->api->url('quotation'))->execute();

        $quote=$this->add('Model_Quote_Guest')->load($this->api->recall('guest_quote_id'));

        if ($this->api->recall('quote_'.$quote['id'])=='sent'){
            $this->add('View_Info')->set('Quote has been sent for estimation. We will contact you as soon as possible. Thank you.');
        }else{
            $this->add('H4')->set('Thank you!');
            $this->add('View')->set('Please fill information about your requirements. You can add file to each requirement (image, document etc).');
            $this->add('View')->set('When you finish to describe your requirements please click button "Submit for Quotation" and our manager contact with.');

            $requirements=$this->add('Model_Requirement_Guest');
            $requirements->addCondition('quote_id',$this->api->recall('guest_quote_id'));


            /* ***************************
             *
             *          HTML
             *
             */
            // | *** LEFT *** |
            $left = $this->add('View')
                ->setClass('left span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
            ;
            $left->add('H1')->set('Requirements for Quotation');

            // quote info grid
            $left->add('H4')->set('Quote:');
            $fields_required = array('project','name',/*'estimated',*/'general_description',);
            $this->addQuoteInfoGrid($left, $fields_required,$quote);

            // requirements
            $left->add('H4')->set('Requirements:');
            $left->add('View_Info')->set('Requirements, which will be added in the future increase estimation.');

            // | *** RIGHT *** |
            $right = $this->add('View')
                ->setClass('right span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
            ;
            $this->addRequestForEstimateButton($right, $requirements, $quote);

            $this->add('View')->setClass('clear');

            // grid with requirements
            $cr = $this->addRequirementsCRUD($this, $quote, $requirements);
            $this->addRequirementForm($this, $quote, $cr);
        }
    }

    function addRequestForEstimateButton($view, $requirements, $quote) {
        if($_GET['action']=='estimation'){
            $quote->set('status','estimate_needed');
            $quote->save();

            $this->api->mailer->addAllManagersReceivers($quote->get('organisation_id'));
            $this->api->mailer->sendMail('guest_sent_quote',array(
                'quotename'=>$quote->get('name'),
                'link'=>$this->api->siteURL().$this->api->url('quotes/rfq/requirements',array('quote_id'=>$quote->get('id'))),
            ));

            $this->api->memorize('quote_'.$quote['id'],'sent');
            $this->api->redirect(null);
        }

        $but=$view->add('Button')->setClass('right')->set('Submit for Quotation','estimation');

        //if ( count($requirements->getRows()) <= 0 ) $but->js(true)->attr('disabled','disabled');
        $but->js('click', array(
            $this->js()->univ()->redirect($this->api->url(null,array('quote_id'=>$quote['id'],'action'=>'estimation')))
        ));
    }

    function addQuoteInfoGrid($v,$fields_required,$quote) {
        $count = 0;
        $source = array();
        foreach ($quote->get() as $key=>$value) {
            if (in_array($key,$fields_required)) {
                $source[$count]['name'] = ucwords($key);
                $source[$count]['value'] = $value;
                $count++;
            }
        }
        $gr = $v->add('Grid');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','Info');
        $gr->addFormatter('value','wrap');
        $gr->setSource($source);

    }

    public $allow_included = true;
    public $edit_fields = array('name','descr','estimate','file_id');
    function addRequirementsCRUD($view, $quote, $requirements) {
        $cr = $view->add('CRUD',
            array(
                'allow_add'    => false, // we cannot add from crud TODO make add from CRUD only
                'allow_edit'   => true,
                'allow_del'    => true,
                'grid_class'   => 'Grid_RequirementsGuest',
                'quote'        =>$quote,
                'total_view'   =>null,
            )
        );

        $cr->setModel($requirements,
            array('name','descr','file_id'),
            array('name','cost','file')
        );

        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->grid->setFormatter('name','wrap');
        }
        return $cr;
    }

    function addRequirementForm($view, $quote, $crud) {
        $view->add('H4')->set('New Requirement:');

        $form=$view->add('Form');
        $m=$this->setModel('Model_Requirement_Guest');
        $form->setModel($m,array('name','descr','file_id'));
        $form->addSubmit('Save');

        if($form->isSubmitted()){
            $form->model->set('quote_id',$quote['id']);
            $form->update();

            $form->js(null,array(
                $crud->js()->trigger('reload'),
            ))->reload()->execute();
        }
    }

}
