<?php
class Client_Quotes extends View {
    public $quotes;
    function init(){
        parent::init();

        $this->api->stickyGET('id');
        $this->api->stickyGET($this->name);


        $v=$this->add('View')->setClass('left');

        $b=$v->add('Button')->set('Request For Quotation');
        $b->js('click', array(
            $this->js()->univ()->redirect($this->api->url('client/quotes/rfq'))
        ));

        $v=$this->add('View')->setClass('clear');

        $this->add('P');

        $cr=$this->add('Grid_Quotes',array(
            'role'=>'client',
            'allowed_actions'=>array(
                'details',
                'edit_details',
                'approve',
            )
        ));
        $m=$this->add('Model_Quote');
        $pr = $m->join('project','project_id','left','_pr');
        $pr->addField('pr_client_id','client_id');
        $m->addCondition('pr_client_id',$this->api->auth->model['client_id']);

        $cr->setModel($m,array('project','user','name','estimated','estimpay','spent_time','rate','currency','durdead','status'));
        $cr->addFormatter('status','status');

        $this->add('P');
    }
}