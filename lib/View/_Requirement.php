<?php
class View_Requirement extends AbstractView {
    private $req;
    private $quote;

    function prepareData($requirement_id){
        $this->req=$this->add('Model_Requirement')->notDeleted()->load($requirement_id);
        $this->quote=$this->add('Model_Quote')->notDeleted()->getThisOrganisation()->load($this->req->get('quote_id'));
        $_GET['project_id']=$this->quote->get('project_id');
    }

    function showHeader(){
        $this->add('x_bread_crumb/View_BC',array(
            'routes' => array(
                0 => array(
                    'name' => 'Home',
                ),
                1 => array(
                    'name' => 'Quotes',
                    'url' => 'quotes',
                ),
                2 => array(
                    'name' => 'Quote',
                    'url' => $this->api->url('quotes/rfq/requirements',array('quote_id'=>$this->quote->get('id'))),
                ),
                3 => array(
                    'name' => 'Details of requirement',
                    'url' => '',
                ),
            )
        ));

        // | *** LEFT *** |
        $left = $this->add('View')
            ->setClass('left span6')
            ->addStyle('margin-top','20px')
            ->addStyle('margin-bottom','20px')
        ;
        $left->add('H1')->set($this->req->get('name'));

        // details
        $left->add('H4')->set('Details:');
        $source = array(
            array(
                'name'=>'Project Name',
                'value'=>$this->quote->get('project'),
            ),
            array(
                'name'=>'Quote Name',
                'value'=>$this->quote->get('name'),
            ),
        );
        $show_fields=$this->quote->whatRequirementFieldsUserCanSee($this->api->currentUser());
        foreach($show_fields as $field){
            if($field!='name' && $field!='count_comments'){
                if($field=='is_included'){
                    $this->req->get($field)==1?$value='Y':$value='N';
                }else{
                    $value=$this->req->get($field);
                }
                $source[]=array(
                    'name'=>$field,
                    'value'=>$value
                );
            }
        }
        $gr = $left->add('Grid_Quote');
        $gr->addColumn('text','name','');
        $gr->addColumn('text','value','Info');
        $gr->addFormatter('value','wrap');
        $gr->addFormatter('value','download');
        $gr->setSource($source);

        $this->add('View')->setClass('clear');
    }

    function showGrids(){
        $this->add('View')->setHtml('<strong>Description:</strong> '.$this->api->colubris->makeUrls(nl2br($this->req->get('descr'))));

        $this->add('View')->setHtml('<hr /><strong>Comments:</strong> ');

        $cr=$this->add('CRUD', array('grid_class'=>'Grid_Reqcomments'));

        $m=$this->add('Model_Reqcomment')->notDeleted()
            ->addCondition('requirement_id',$this->req['id']);
        $cr->setModel($m,
            array('text','file_id'),
            array('text','user','file','file_thumb','created_dts')
        );
        if($cr->grid){
            $cr->grid->addClass('zebra bordered');
            $cr->add_button->setLabel('Add Comment');
            //$cr->grid->setFormatter('text','text');
        }

        $this->tasks=$this->add('Model_Task_RestrictedUsers');
        $this->tasks->addCondition('project_id',$this->req['project_id']);
        $this->tasks->addCondition('requirement_id',$this->req['id']);
        $this->addTasksCRUD($this);

    }

    function addTasksCRUD($view) {
        $view->add('View')->setHtml('<strong>Tasks:</strong>');
        $user = $this->api->currentUser();
        if (!$this->quote->isExpired()){
            $allow_add=true;
            $can_edit=true;
            $can_del=true;
        }else{
            $allow_add=false;
            $can_edit=false;
            $can_del=false;
        }
        $cr = $view->add('CRUD', array(
            'grid_class'      => 'Grid_Tasks',
            'allow_add'       => $allow_add,
            'allow_edit'      => $can_edit,
            'allow_del'       => $can_del,
        ));

        if ($cr->grid) {
            $cr->grid->quote = $this->quote;
        }

        $cr->setModel(
            $this->tasks,
            $this->app->user_access->whatTaskFieldsUserCanEdit(),
            $this->app->user_access->whatTaskFieldsUserCanSee($this->quote)
        );
    }

}