<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/26/13
 * Time: 6:22 PM
 * To change this template use File | Settings | File Templates.
 */
class Grid_Requirements extends Grid_CountLines {
    private $quote;
    private $cannot_toggle_statuses = array('estimation_approved','finished',);
    private $can_toggle = false;
    public $total_view;
    function init() {
        parent::init();
        $this->quote = $this->owner->quote;
        $this->total_view = $this->owner->total_view;
        $this->can_toggle = (in_array($this->quote['status'],$this->cannot_toggle_statuses)?false:true);

        $this->js('reload',array(
            $this->total_view->js()->trigger('reload')
        ))->reload();
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
        if ($this->hasColumn('count_comments')) {
            $this->getColumn('count_comments')->setCaption('Comm.');
        }
        $this->getColumn('is_included')->setCaption('✔');

        if ($this->can_toggle) {
            $this->js(true)->colubris()->toggle_is_included(
                $this->api->url(null,array('toggle'=>1)),
                array('#'.$this->name,'.estimate_total_time_to_reload')
            );
        }

        if ($_GET['toggle']) {
            $this->toggleRequirement();
            throw $this->exception('toggleRequirement should return some javascript');
        }

    }
    function setCaption($name) {
        $this->columns[$this->last_column]['descr'] = $name;
        return $this;
    }
    function formatRow() {
        parent::formatRow();

        // http://vazelin.org.ua/archives/1573/utf-8-simvoly/
        if ( $this->current_row['is_included'] && $this->current_row['is_included'] !== 'N' ) {
            $this->current_row_html['is_included'] =
                '<div id="is_included_'.$this->current_row['id'].'" data-id="'.$this->current_row['id'].'" class="toggle_is_included active" style="'.($this->can_toggle?'cursor:pointer':'').'"  align=center>'.
                    '☑'.
                '</div>';
        } else {
            $this->current_row_html['is_included'] =
                '<div id="is_included_'.$this->current_row['id'].'" data-id="'.$this->current_row['id'].'" class="toggle_is_included not-active" style="cursor: pointer;" align=center>'.
                    '<span class="">☐</span>'.
                '</div>';
        }
    }
    function toggleRequirement() {
        if ($_POST['req_id']) {
            if (!isset($_GET['quote_id'])) {
                throw $this->exception('Provide $_GET[\'quote_id\']');
            }

            $quote = $this->add('Model_Quote')->tryLoad($_GET['quote_id']);
            if (!$quote->loaded()) {
                $this->js()->univ()->errorMessage('Thete is no such a quote. id: '.$_GET['quote_id']);
            }

            if ( !$this->can_toggle ) {
                throw $this->exception('You cannot change list of requirements for this quote.')
                    ->addMoreInfo('status',$quote['status']);
            }

            $requirements=$this->add('Model_Requirement');
            $requirements->addCondition('quote_id',$_GET['quote_id']);
            $requirements->tryLoad($_POST['req_id']);
            if (!$requirements->loaded()) {
                $this->js()->univ()->errorMessage('Thete is no such a requirement. id: '.$_POST['req_id']);
            }
            $requirements->set('is_included',
                ($requirements->get('is_included')==true)?false:true
            );
            $requirements->save();
            $this->js(null,array(
                $this->js()->trigger('reload'),
                //$this->js()->_selector('.estimate_total_time_to_reload')->reload()
            ))->execute();

        } else {
            throw $this->exception('Provide $_POST[\'req_id\'] if there is $_GET[\'toggle\']');
        }

    }
}