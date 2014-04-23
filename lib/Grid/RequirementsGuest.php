<?php
class Grid_RequirementsGuest extends Grid_CountLines {
    private $quote;
    private $can_toggle = false;
    private $reload_object;
    function init() {
        parent::init();
        $this->addClass('zebra bordered');
        $this->quote = $this->owner->quote;

        if (is_subclass_of($this->owner, 'CRUD')) {
            $this->reload_object = $this->owner;
        } else {
            $this->reload_object = $this;
        }
    }
    function setModel($model, $actual_fields = UNDEFINED) {
        parent::setModel($model, $actual_fields);
    }
    function formatRow() {
        parent::formatRow();

        // http://vazelin.org.ua/archives/1573/utf-8-simvoly/
        if ( $this->current_row['is_included'] && $this->current_row['is_included'] !== 'N' ) {
            $this->current_row_html['is_included'] =
                '<div id="is_included_'.$this->current_row['id'].'"
                    data-id="'.$this->current_row['id'].'"
                    class="toggle_is_included active"
                    align=center>'.
                    '☑'.
                '</div>';
        } else {
            $this->current_row_html['is_included'] =
                '<div id="is_included_'.$this->current_row['id'].'"
                    data-id="'.$this->current_row['id'].'"
                    class="toggle_is_included not-active"
                    align=center>'.
                    '<span class="">☐</span>'.
                '</div>';
        }
    }
    function toggleRequirement() {
        if ($_POST['req_id']) {
            if (!isset($_GET['quote_id'])) {
                throw $this->exception('Provide $_GET[\'quote_id\']');
            }

            $quote = $this->add('Model_Quote')->notDeleted()->getThisOrganisation()->tryLoad($_GET['quote_id']);
            if (!$quote->loaded()) {
                $this->js()->univ()->errorMessage('There is no such a quote. id: '.$_GET['quote_id']);
            }

            $requirements=$this->add('Model_Requirement')->notDeleted();
            $requirements->addCondition('quote_id',$_GET['quote_id']);
            $requirements->tryLoad($_POST['req_id']);
            if (!$requirements->loaded()) {
                $this->js()->univ()->errorMessage('There is no such a requirement. id: '.$_POST['req_id']);
            }
            $requirements->set('is_included',
                ($requirements->get('is_included')==true)?false:true
            );
            $requirements->save();
            $this->js(null,array(
                $this->reload_object->js()->trigger('reload'),
            ))->execute();

        } else {
            throw $this->exception('Provide $_POST[\'req_id\'] if there is $_GET[\'toggle\']');
        }

    }
}