<?php
class page_system_system extends Page {
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
                    'name' => 'System Admins',
                    'url' => 'system/system',
                ),
            )
        ));

        $this->add('H1')->set('System Admins');

        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_User_Sys')->setOrder('name');

        $crud->setModel($model,
            array('name','email','password'),
            array('name','email')
        );

//        if($crud->grid){
//            $crud->grid->addColumn('button','login');
//            if($_GET['login']){
//                $u=$this->add("Model_User_Sys")->load($_GET['login']);
//                $u->set('hash',md5(time()));
//                $u->save();
//                $this->js(true)->univ()->location($this->api->url("index",array('id'=>$_GET['login'],'hash'=>$u->get('hash'))))->execute();
//            }
//        }
    }
}
