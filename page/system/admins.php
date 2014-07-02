<?php
class page_system_admins extends Page {
    function init(){
        parent::init();

    }
    function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Admins',
                    'url' => 'system/admins',
                ),
            )
        ));

        $this->add('H1')->set('Admins');

        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_User')/*->debug()*/->addCondition('is_admin',true)->setOrder('name');

        $crud->setModel($model,
            array('name','email','client_id','is_manager','is_developer','password'),
            array('name','email','client','is_admin','is_manager','is_developer','is_client')
        );

        if($crud->grid){
            $crud->grid->addClass('zebra bordered');
            $crud->grid->addColumn('button','login');
            if($_GET['login']){
                $u=$this->add("Model_User")->load($_GET['login']);
                $u->set('hash',md5(time()));
                $u->save();
                $this->js(true)->univ()->location($this->api->url("index",array('id'=>$_GET['login'],'hash'=>$u->get('hash'))))->execute();
            }
        }
    }
}
