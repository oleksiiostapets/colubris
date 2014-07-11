<?php
class page_api_project extends Page_api_general {

    function init() {
        parent::init();
        $this->m = $this->add('Model_Project');
    }

    function page_getParticipants() {
        $id = $this->checkGetParameter('id');
        $mp = $this->add('Model_Participant');
        $mp->addCondition('project_id',$id);
        if($this->getParameter('name')){
            $name = $this->getParameter('name');
            $mp->addCondition('user','LIKE',$name.'%');
        }
        $data = $mp->getRows();
        echo json_encode([
            'result' => 'success',
            'data'   => $data,
        ]);
        exit();
    }

}