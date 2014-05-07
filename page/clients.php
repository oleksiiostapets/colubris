<?
class page_clients extends Page {

    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if( !$this->api->user_access->canUserMenageClients() ){
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
                    'name' => 'Clients',
                    'url' => 'clients',
                ),
            )
        ));

        $this->add('H2')->set('Clients');

        $crud=$this->add('CRUD');
        $crud->setModel('Client',
            array('name','email','phone','is_archive')
        );

        if($crud->grid){
            $crud->grid->addClass('zebra bordered');
            $crud->grid->addFormatter('name','fullwidth');
			$crud->grid->add('VirtualPage')
				->addColumn('users')
				->set(function($page){
					$id = $_GET[$page->short_name.'_id'];
					$users = $this->add('Model_User')->getActive()->addCondition('client_id',$id);
					$cr_users = $page->add('CRUD',array('allow_edit'=>false,'allow_del'=>false,'allow_add'=>false));
					$cr_users->setModel($users,array('email','name'));
				});
//            $crud->grid->addColumn('expander','users','Users');
            //$crud->grid->addButton('Import from SortMyBooks')->js('click')
            //    ->univ()->frameURL('Import from SortMyBooks...',$this->api->getDestinationURL('./smboimport'));
        }
    }
    function page_users(){
        $this->api->stickyGET('client_id');
        $m=$this->add('Model_User')->getUsersOfOrganisation()
            ->setMasterField('client_id',$_GET['client_id'])
            ;

        $cr=$this->add('CRUD');
        $cr->setModel($m,
            array('email','name','password'),
            array('email','name')
        );

    }
}
