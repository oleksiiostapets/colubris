<?php
class page_developers extends Page {
	function init(){
		parent::init();

	}
    function page_index() {


        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Developers',
                    'url' => 'admin/developers',
                ),
            )
        ));

        $this->add('H1')->set('Developers');

        $m=$this->add('Model_Developer');
      		$m->getField('name')->readonly(true);
      		$m->getField('email')->readonly(true);
      		$m->getField('weekly_target')->readonly(false);

      		$this->add('CRUD')->setModel($m,array('name','email','weekly_target'));
    }
}
