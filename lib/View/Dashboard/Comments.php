<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/25/14 5:16 PM
 */
abstract class View_Dashboard_Comments extends CompleteLister {
    protected $type;
    /**
     * Paginator object
     *
     * @see addPaginator()
     */
    protected $paginator = null;

    function init() {
        parent::init();
        $this->setModel($this->app->currentUser()->getDashboardCommentsModel($this->type));

        $type_low = strtolower($this->type);
        if($comment_id = $_GET['mark_'.$type_low.'_as_read']) {
            $comment_user = $this->add('Model_'.$this->type.'commentUser');
            $comment_user->set($type_low.'comment_id',$comment_id);
            $comment_user->set('user_id',$this->app->currentUser()->id);
            $comment_user->save();

            $this->js()->reload()->execute();
        }

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

        // move to archive button
        $b = $this->add('Button',null,'mark_as_read_button')->addClass('atk-button-small atk-effect-danger')->set('Mark as read');
        $b->js('click')->univ()->ajaxec($this->app->url(null,array(
            'mark_'.strtolower($this->type).'_as_read' => $this->current_row['id'],
        )));
        $this->current_row_html['mark_as_read_button'] = $b->getHtml();
    }
    function defaultTemplate() {
        return array('view/dashboard/discussion');
    }
}