<?
class page_manager_clients extends Page {

	function page_index(){

        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Clients',
                    'url' => 'manager/clients',
                ),
            )
        ));

        $this->add('H2')->set('Clients');

        $crud=$this->add('CRUD');
        $crud->setModel('Client');

        if($crud->grid){
            $crud->grid->addFormatter('name','fullwidth');
            $crud->grid->addColumn('expander','users','Users');
            //$crud->grid->addButton('Import from SortMyBooks')->js('click')
            //    ->univ()->frameURL('Import from SortMyBooks...',$this->api->getDestinationURL('./smboimport'));
        }
    }
    function page_smboimport(){
        $data=(array)$this->add('Controller_SMBO')->get('client');

        $m=$this->add('Model_Client');


        foreach($data as $id=>$row){

            $row=(array)$row;
            $row['smbo_id']=$row['id'];unset($row['id']);
            $row['name']=$row['legal_name'];unset($row['legal_name']);
            $row['email']=$row['email_address'];unset($row['email_address']);
            $data[$id]=$row;
        }

        $data2=array();

        foreach($data as $row){
            $m->unloadData();
            $m->loadBy('smbo_id',$row['smbo_id']);
            $m->set($row)->update();
            $data2[]=$row;
            var_dump($row);
        }

        $this->add('H2')->set('List of imported clients');

        $g=$this->add('Grid');
        $g->setModel('Client');
        $g->dq=null;
        unset($g->columns['is_archive']);
        unset($g->columns['project_count']);
        $g->setStaticSource($data2);
        //$g->addColumn('text','smbo_id');
        //$g->addColumn('text','name');
        //$g->addColumn('text','name');
    }
    function page_users(){
        $this->api->stickyGET('client_id');
        $m=$this->add('Model_User')
            ->setMasterField('client_id',$_GET['client_id'])
            ;

        $cr=$this->add('CRUD');
        $cr->setModel($m,array('email','name','hourly_cost','daily_cost'));
        if($cr->grid){
            $cr->grid->addColumn('button','reset','Reset Password');

            if($_GET['reset']){
                $m->loadData($_GET['reset']);
                $m->resetPassword();

                $cr->grid->js()->univ()->successMessage('New password sent to email: "'.$m->get('email').'"')->execute();

            }
        }

    }
}
class Controller_SMBO extends AbstractController {
    function call($ctl,$command,$args=array()){
        $ch = curl_init();
        //$url=$this->api->getConfig('api_url','http://stage1.as01.demo.agiletech.ie/api/json');
        $url=$this->api->getConfig('smbo/url');//'http://smbo.local.agiletech.ie/api/json';
        $url.='/'.$ctl;
        $url.='/'.$command;

        $args['hash']=$this->api->getConfig('smbo/hash');
        $args['system_id']=$this->api->getConfig('smbo/system_id');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, count($args));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);


        $res=curl_exec($ch);
        curl_close($ch);
        if(is_null(json_decode($res))){
            $res='URL: '.$url.'<br/>'.print_r($args,true).'<br/><hr/>'.$res;

        }else{
            //if($args['system_id'])-$this->api->memorize('system_id',$args['system-id']);
        }
        return $res;
    }
    function get($ctl){
        $data=json_decode($res=$this->call($ctl,'list'));
        if(!$data){
            $this->owner->add('View_Error')->set($res);
            return array('Error');
        }
        return $data;
    }
}

