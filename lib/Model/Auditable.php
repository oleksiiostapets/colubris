<?php

class Model_Auditable extends Model_BaseTable {
	public $modify_action;
	function init(){
		parent::init();

        $this->addHook('beforeModify',function($m){
            $m->modify_action = $m->refLog()
                ->set('new_data',json_encode($m->get()))
                ->set('changed_fields',json_encode($m->dirty))
                ;
        });

        $this->addHook('afterModify',function($m){
            $m->modify_action->save();
        });

        $this->addHook('afterInsert',function($m){
            $m->refLog()
                ->set('new_data',json_encode($m->get()))
                ->set('changed_fields',json_encode(array('inserted'=>true)))
                ->save();
        });

        $this->addHook('beforeDelete',function($m){
            $m->delete_action = $m->refLog('Model_Log',false)
                ->set('new_data',json_encode($m->get()))
                ->set('changed_fields',json_encode(array('deleted'=>true)))
                ;
        });

        $this->addHook('afterDelete',function($m){
            $m->delete_action->save();
        });
    }

    function refLog($model='Model_Log',$need_rec_id=true){
        $m= $this->add($model);
        $m->addCondition('class',get_class($this));
        if ($need_rec_id) $m->addCondition('rec_id',$this->id);
        return $m;
    }
}
