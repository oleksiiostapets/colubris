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
        $this->removeEstimateColumnIfNeeded();
        $this->addFormatter('project','wrap');
        $this->addFormatter('name','wrap');
    }
    function formatRow() {
        parent::formatRow();

        // name
        $this->current_row_html['name'] =
            '<div class="name">
                <a href="'.$this->api->url('task',array(
                                'task_id'=>$this->current_row['id'],'requirement_id'=>null
            )).'">'.$this->current_row['name'].'</a></div>'
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
                return '.atk-effect-danger';
   			case 'accepted':
                return '.atk-effect-success';
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
    private function removeEstimateColumnIfNeeded() {
        if (
            in_array('estimate',$this->model->whatFieldsUserCanSee($this->api->currentUser(),$this->quote)) &&
            in_array('spent_time',$this->model->whatFieldsUserCanSee($this->api->currentUser(),$this->quote))
        ) {
            $this->removeColumn('estimate');
        }
    }
}