<?
class page_admin_users extends Page {
    function init(){
        parent::init();

    }
    
    function page_index(){
        $crud=$this->add('CRUD');
        
        $model = $this->add('Model_User')->setOrder('name');

        $crud->setModel($model,
                        array('email','name','client_id','is_admin','is_manager','is_developer','is_timereport','password'),
                        array('email','name','client','is_admin','is_manager','is_developer','is_timereport','is_client'));

        if($crud->grid){
            //$crud->grid->addColumn('expander','projects');
            
            $crud->grid->addColumn('button','login', array("descr" => "Log in"));
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
                array('budget_id','user_id','role'),
                array('budget','user','role')
                );
    }
}
