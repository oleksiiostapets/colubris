<?php
class Grid_Tasks extends Grid_Advanced {
    public $quote;
    function init() {
        parent::init();
        $this->js('reload')->reload();
        $this->addClass('zebra bordered');
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        $this->removeColumnsIfNeeded();
    }
    function addPaginator($ipp = 25, $options = null) {
        $this->app->stickyGet('project');
        $this->app->stickyGet('quote');
        $this->app->stickyGet('requirement');
        $this->app->stickyGet('status');
        $this->app->stickyGet('assigned');
        parent::addPaginator($ipp, $options);

        return $this;
    }
    function formatRow() {
        $this->app->stickyForget('project');
        $this->app->stickyForget('quote');
        $this->app->stickyForget('requirement');
        $this->app->stickyForget('status');
        $this->app->stickyForget('assigned');
        parent::formatRow();

        // name
        $this->current_row_html['name'] =
            '
            <div class="name"><span>#'.$this->current_row['id'].'</span>
                <a href="'.$this->api->url('task',array(
//	                            'project'=>$this->current_row['project_id'],
//	                            'quote'=>$this->current_row['quote_id'],
//					            'requirement'=>$this->current_row['requirement_id'],
					            'task_id'=>$this->current_row['id'],
	                            'requirement_id'=>null
            )).'">'.$this->current_row['name'].'</a></div>
            <div class="project">Project: '.$this->current_row['project'].'</div>
            <div class="quote">Quote: '.
                    ($this->current_row['quote']?$this->current_row['quote']:'---')
            .'</div>
            <div class="requirement">Requirement: '.
                    ($this->current_row['requirement']?$this->current_row['requirement']:'---')
            .'</div>
            '
        ;

        // spent_time
        if ($this->current_row['spent_time'] == '') {
            $this->current_row['spent_time'] = '0.00';
        }
        $this->current_row_html['spent_time'] =
                '<div style="white-space:wrap;" class="spent_time">'.$this->current_row['spent_time'].'</div>'.
                '<div style="white-space:wrap;" class="estimate">Est: ('.$this->current_row['estimate'].')</div>'
        ;

        // requester
        $this->current_row_html['requester'] = '<div class="requester">'.$this->current_row['requester'].'</div>';

        // assigned
        $this->current_row_html['assigned'] = '<div class="assigned">'.$this->current_row['assigned'].'</div>';

        // updated_dts
        $this->current_row_html['updated_dts'] = '<div class="updated">'.$this->current_row['updated_dts'].'</div>';

        // status
        $this->current_row_html['status'] =
                '<div class="atk-label '.$this->getStatusClass($this->current_row['status']).'">'.$this->current_row['status'].'</div>';

        // priority
        $this->current_row_html['priority'] =
                '<div class="atk-label '.$this->getPriorityClass($this->current_row['priority']).'">'.$this->current_row['priority'].'</div>';

        // type
        $this->current_row_html['type'] =
                '<div class="atk-label '.$this->getTypeClass($this->current_row['type']).'">'.$this->current_row['type'].'</div>';

    }
    function getStatusClass($status) {
        switch($status){
       		case 'started':
                return ' atk-effect-info';
       		case 'finished':
                return ' atk-effect-warning';
            case 'tested':
                return ' atk-effect-success';
            case 'rejected':
                return ' atk-effect-danger';
   			case 'accepted':
                return ' atk-effect-success';
   			case 'unstarted':
                return ' ';
       		default:
                return '';
       	}
    }
    function getTypeClass($status) {
        switch($status){
       		case 'project':
                return ' atk-effect-info';
       		case 'change request':
                return ' atk-effect-success';
            case 'bug':
                return ' atk-effect-danger';
            case 'support':
                return ' atk-effect-info';
   			case 'drop':
                return ' atk-effect-warning';
       		default:
                return '';
       	}
    }
    function getPriorityClass($status) {
        switch($status){
       		case 'low':
                return ' atk-effect-info';
       		case 'normal':
                return ' atk-effect-warning';
            case 'high':
                return ' atk-effect-danger';
       		default:
                return '';
       	}
    }
    private function removeColumnsIfNeeded() {
		$this->removeColumn('quote_id');
        $this->removeColumn('project');
        $this->removeColumn('quote');
    }
}