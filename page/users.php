<?php
class page_users extends Page {
    function init(){
        parent::init();
        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->currentUser()->canSeeUserList() ){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

    }
    
    function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Users',
                    'url' => 'admin/users',
                ),
            )
        ));

        $this->add('H1')->set('Users');

        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_User_Organisation')->setOrder('name');

        $crud->setModel($model,
            array('email','name','password','is_admin','is_manager','is_sales','is_financial','is_developer','client_id'),
            array('email','name','client','is_admin','is_manager','is_sales','is_financial','is_developer','is_client')
        );

        if($crud->grid){
            $crud->grid->addClass('zebra bordered');
            //$crud->grid->addColumn('expander','projects');
            
            $crud->grid->addColumn('button','login');
            if($_GET['login']){
                $u=$this->api->currentUser();
                $u->set('hash',md5(time()));
                $u->save();

                setcookie("fuser",$u['id'],time()+60*60*24);
                setcookie("fhash",$u['hash'],time()+60*60*24);

                $u=$this->add("Model_User_Organisation")->load($_GET['login']);
                $u->set('hash',md5(time()));
                $u->save();
                $this->js(true)->univ()->location($this->api->url("index",array('id'=>$_GET['login'],'hash'=>$u->get('hash'))))->execute();
            }
        }
    }
    
    // "Expander" pages
    function page_projects(){
        $this->api->stickyGet('user_id');
        $m=$this->add('Model_Participant')->tryLoadBy('user_id',$_GET['user_id']);
        $this->add('CRUD')->setModel($m,
            array('budget_id','user_id','role'),
            array('budget','user','role')
        );
    }
}
