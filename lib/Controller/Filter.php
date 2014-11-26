<?php
/**
 * Created by Vadym Radvansky
 * Date: 3/27/14 2:57 PM
 */
class Controller_Filter extends AbstractController {

    public $auto_track_element=true;
    protected $views = array();
    protected $values = array();
    protected $form;

    public function addViewToReload($view) {
        $this->views[$view->name] = $view;
        return $this;
    }
    public function setForm($form) {
        $this->form = $form;
        return $this;
    }
    public function stickyGetFormVars() {
        if (!$this->form) throw $this->app->exception('Provide Form for filter with setForm()');
        foreach ($this->form->get() as $k=>$v) {
            if ($_GET[$k]) {
                $this->app->stickyGet($k);
            }
        }
    }
    public function set($k,$v) {
        $this->values[$k] = $v;
        return $this;
    }
    public function get($k=null) {
        if (!$k) return $this->values;
        if (array_key_exists($k,$this->values)) {
            return $this->values[$k];
        }
        return false;
    }
    public function commit() {
        if (!$this->form) throw $this->app->exception('Provide Form for filter with setForm()');
        if ($this->form->isSubmitted()) {
            $this->hook('is-submitted');
            foreach ($this->form->get() as $k=>$v) {
                if ($this->get($k) !== false) {
                    $_GET[$k] = $this->get($k);
                } else {
                    $_GET[$k] = $v;
                }
                $this->app->stickyGet($k);
            }
            //$this->resetPaginators();
            $js = $this->getReloadJs();
            $js[] = 'if (typeof(history.pushState) != "undefined") {window.history.pushState("filtered.html", "Filter reloaded page", "'.$this->app->url().'");};';
            $this->app->js(null,$js)->execute();
        }
    }

    protected function resetPaginators($arr=null,$path='') {
        if (!$arr) $arr = $_SESSION;
        foreach ($arr as $k=>$v) {
            if (is_array($v) && count($v)) {
                $cur_path =  $path.'["'.$k.'"]';
                $this->resetPaginators($v,$cur_path);
            } else {
                if ($k == 'skip') {
                    $cur_path =  $path.'["'.$k.'"]';
                    $com = 'unset($_SESSION' .$cur_path.');';
                    eval($com);
                }
            }
        }
    }

    protected function getReloadJs() {
        $js = array();
        foreach ($this->views as $view) {
            $js[] = $view->js()->reload();
        }
        return $js;
    }

}