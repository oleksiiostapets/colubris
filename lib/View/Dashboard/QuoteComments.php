<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/25/14 2:52 PM
 */
class View_Dashboard_QuoteComments extends CompleteLister {
    /**
     * Paginator object
     *
     * @see addPaginator()
     */
    protected $paginator = null;

    function init() {
        parent::init();
        $this->setModel($this->app->currentUser()->getDashboardCommentsToReqModel());
        $this->addPaginator(5);
    }
    function addPaginator($ipp = 25, $options = null) {
        // adding ajax paginator
        if ($this->paginator) {
            return $this->paginator;
        }
        $this->paginator = $this->add('Paginator', $options);
        $this->paginator->ipp($ipp);
        return $this;
    }
    function formatRow() {

        // thumb or download link
        if ($this->current_row['file_thumb'] != '') {
            $this->current_row_html['file'] = '<a target="_blank" href="'. $this->current_row['file'] .'"><img width="50" src="'.$this->current_row['file_thumb'].'"></a>';
        } else {
            if ($this->current_row['file'] != '') {
                $this->current_row_html['file'] = '<a target="_blank" href="'. $this->current_row['file'] .'">download</a>';
            } else {
                $this->current_row_html['file'] = '';
            }
        }

        // timestamp
        if ($this->current_row['created_dts']) {
            $this->current_row_html['timestamp'] = '<div class="timestamp">'.$this->current_row['created_dts'].'</div>';
        } else {
            $this->current_row_html['timestamp'] = '---';
        }

        // user
        if ($this->current_row['user']) {
            $this->current_row_html['user'] = $this->current_row['user'];
        } else {
            $this->current_row_html['user'] = 'Unknown user';
        }

        // text
        if ($this->current_row['text']) {
            $this->current_row_html['text'] = nl2br($this->current_row['text']);
        } else {
            $this->current_row_html['text'] = '---';
        }

        // project
        if ($this->current_row['project_name']) {
            $this->current_row_html['project'] = $this->current_row['project_name'];
        } else {
            $this->current_row_html['project'] = 'none';
        }

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

        // move to archive button
        $b = $this->add('Button',null,'mark_as_read_button')->addClass('atk-button-small atk-effect-danger')->set('Mark as read');
        $b->js('click')->univ()->ajaxec($this->app->url(null,array(
            'mark_as_read' => $this->current_row['id'],
        )));
        $this->current_row_html['mark_as_read_button'] = $b->getHtml();

        if($_GET['mark_as_read']){
            $comment_user = $this->add('Model_ReqcommentUser');
            $comment_user->set('reqcomment_id',$_GET['mark_as_read']);
            $comment_user->set('user_id',$this->app->currentUser()->id);
            $comment_user->save();

            $this->js()->reload()->execute();
        }
    }
    function defaultTemplate() {
        return array('view/dashboard/quote_comments');
    }
}