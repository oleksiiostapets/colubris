<?php
class page_developers extends Page {
	function init(){
		parent::init();
        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->app->model_user_rights->canSeeDevelopers() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

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
      		//$m->getField('weekly_target')->readonly(false);

      		$this->add('CRUD')->setModel($m,array('name','email'));
    }
}
