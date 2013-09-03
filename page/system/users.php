<?
class page_system_users extends Page {
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
                    'name' => 'Users',
                    'url' => 'admin/users',
                ),
            )
        ));

        $this->add('H1')->set('Users');

        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_User')->setOrder('name');

        $crud->setModel($model,
            array('email','name','organisation_id','is_admin','is_manager','is_developer','client_id','password'),
            array('email','name','organisation','is_admin','is_manager','is_developer','client')
        );

        if($crud->grid){
            $crud->grid->addClass('zebra bordered');
            //$crud->grid->addColumn('expander','projects');
            
            $crud->grid->addColumn('button','login');
            if($_GET['login']){
                $u=$this->add("Model_User")->load($_GET['login']);
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
                array('budget_id','user_id','role','hourly_rate'),
                array('budget','user','role','hourly_rate')
                );
    }
}
