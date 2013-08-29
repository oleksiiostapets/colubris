<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/28/13
 * Time: 11:53 PM
 * To change this template use File | Settings | File Templates.
 */
class Page_Step2 extends page_quotesfunctions {

    function page_index(){


        /* ***************************
         *
         *         PREPARATION
         *
         */
        if (!isset($_GET['quote_id'])) throw $this->exception('Provide $_GET[\'quote_id\']');
    	$this->api->stickyGet('quote_id');
        $quote = $this->add('Model_Quote')->load($_GET['quote_id']); // TODO nice UI to show user if there is no quote

        // Does Project of this quotetion exist?
        $project=$this->add('Model_Project')->tryLoad($quote->get('project_id'));
        if( !$project->loaded() ){
        	$this->api->redirect('/denied');
        	// TODO nice UI to explain user that there is no such a project
            // TODO just redirect to denied is not clear
        }

        // Checking client's permission to this quote and redirect to denied if required
        if( !$quote->hasUserEditRequirementsAccess($this->api->auth->model) ){
        	$this->api->redirect('/denied');
        }

        $requirements=$this->add('Model_Requirement');
        $requirements->addCondition('quote_id',$_GET['quote_id']);




        /* ***************************
         *
         *          HTML
         *
         */
        $this->addBreacrumb($this,$this->role.'/quotes');

        // | *** LEFT *** |
        $left = $this->add('View')
                ->setClass('left span6')
                ->addStyle('margin-top','20px')
                ->addStyle('margin-bottom','20px')
        ;
        $left->add('H1')->set('Requirements for Quotation');

        // auote info grid
        $left->add('H4')->set('Quote:');
        $fields_required = array('project','user','name',/*'estimated',*/'general',);
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
        // only for non step2 pages >>> $this->addEditRequirementButton($right, $quote, $this->role);
        $total_view = $this->addFloatingTotal($right,$quote);


        $this->add('View')->setClass('clear');

        // grid with requirements
        $this->addRFQRequirements($this, $quote, $requirements, $this->requirements_rights, $total_view);

    }

}