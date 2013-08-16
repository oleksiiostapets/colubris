<?php

class page_deletedfunctions extends Page {
    function page_projects(){
        $m=$this->add('Model_Project_Deleted');

        $cr=$this->add('CRUD',array(
                'grid_class'=>'Grid',
                'allow_add'=>false,
                'allow_edit'=>false,
                'allow_del'=>false)
        );
        $cr->setModel($m,
            array('name','descr','client','demo_url','prod_url')
        );

        if($cr->grid){
            $cr->grid->addPaginator();

            $cr->grid->addColumn('button','restore');
            if ($_GET['restore']) {
                $m=$this->add('Model_Project_Base');
                $o=$m->load($_GET['restore']);
                $o->set('is_deleted',false);
                $o->save();
                $cr->grid->js('reload')->reload()->execute();
            }
        }
    }

    function page_quotes(){
        $m=$this->add('Model_Quote_Deleted');

        $cr=$this->add('CRUD',array(
                'grid_class'=>'Grid',
                'allow_add'=>false,
                'allow_edit'=>false,
                'allow_del'=>false)
        );
        $cr->setModel($m,
            array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status')
        );

        if($cr->grid){
            $cr->grid->addPaginator();

            $cr->grid->addColumn('button','restore');
            if ($_GET['restore']) {
                $m=$this->add('Model_Quote_Base');
                $o=$m->load($_GET['restore']);
                $o->set('is_deleted',false);
                $o->save();
                $cr->grid->js('reload')->reload()->execute();
            }
        }
    }

    function page_tasks(){
        $m=$this->add('Model_Task_Deleted');

        $cr=$this->add('CRUD',array(
                'grid_class'=>'Grid',
                'allow_add'=>false,
                'allow_edit'=>false,
                'allow_del'=>false)
        );
        $cr->setModel($m,
            array('name','priority','type','status','estimate','spent_time','requester','assigned')
        );

        if($cr->grid){
            $cr->grid->addPaginator();

            $cr->grid->addColumn('button','restore');
            if ($_GET['restore']) {
                $m=$this->add('Model_Task_Base');
                $o=$m->load($_GET['restore']);
                $o->set('is_deleted',false);
                $o->save();
                $cr->grid->js('reload')->reload()->execute();
            }
        }
    }

    function page_users(){
        $m=$this->add('Model_User_Deleted');

        $cr=$this->add('CRUD',array(
                'grid_class'=>'Grid',
                'allow_add'=>false,
                'allow_edit'=>false,
                'allow_del'=>false)
        );
        $cr->setModel($m,
            array('email','name','client','is_admin','is_manager','is_developer','is_timereport','is_client')
        );

        if($cr->grid){
            $cr->grid->addPaginator();

            $cr->grid->addColumn('button','restore');
            if ($_GET['restore']) {
                $m=$this->add('Model_User_Base');
                $o=$m->load($_GET['restore']);
                $o->set('is_deleted',false);
                $o->save();
                $cr->grid->js('reload')->reload()->execute();
            }
        }
    }

    function page_clients(){
        $m=$this->add('Model_Client_Deleted');

        $cr=$this->add('CRUD',array(
                'grid_class'=>'Grid',
                'allow_add'=>false,
                'allow_edit'=>false,
                'allow_del'=>false)
        );
        $cr->setModel($m
        );

        if($cr->grid){
            $cr->grid->addPaginator();

            $cr->grid->addColumn('button','restore');
            if ($_GET['restore']) {
                $m=$this->add('Model_Client_Base');
                $o=$m->load($_GET['restore']);
                $o->set('is_deleted',false);
                $o->save();
                $cr->grid->js('reload')->reload()->execute();
            }
        }
    }
}
