<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/25/14 2:52 PM
 */
class View_Dashboard_QuoteComments extends View_Dashboard_Comments {
    protected $type = 'Req';
    function init() {
        parent::init();
        $this->template->del('quote_del');
    }
    function formatRow() {
        parent::formatRow();
        // quote
        if ($this->current_row['quote_name']) {
            $this->current_row_html['quote'] =
                '<a href="'.$this->app->url('/quotes/rfq/requirements',array('quote_id'=>$this->current_row['quote_id'])).'">'
                        .$this->current_row['quote_name'].'</a>';
        } else {
            $this->current_row_html['quote'] = 'none';
        }

        // requirement
        if ($this->current_row['requirement_name']) {
            $this->current_row_html['requirement'] =
                '<a href="'.$this->app->url('quotes/rfq/requirements/more',
                    array(
                        'show_header'=>'true',
                        'expanded'=>'colubris_quotes_rfq_requirements_crud_grid_requirements',
                        'requirement_id'=>$this->current_row['requirement_id'])
                ).'">'.$this->current_row['requirement_name'].'</a>';
        } else {
            $this->current_row_html['requirement'] = 'none';
        }
    }
}