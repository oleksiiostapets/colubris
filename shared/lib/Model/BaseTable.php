<?php
class Model_BaseTable extends Model_Table {
    function delete($id=null){
        if(!is_null($id))$this->load($id);
        if(!$this->loaded())throw $this->exception('Unable to determine which record to delete');

        $tmp=$this->dsql;

        $this->initQuery();
        $delete=$this->dsql->where($this->id_field,$this->id);

        $delete->owner->beginTransaction();
        $this->hook('beforeDelete',array($delete));
        $this->update(array('is_deleted'=>'1'));
        //$this->hook('afterDelete');
        $delete->owner->commit();

        $this->dsql=$tmp;
        $this->unload();

        return $this;
    }
    function forceDelete($id=null){
        return parent::delete($id);
    }
    function getRowsForCurrentUser(){
        $this->checkRights();
        return $this->getRows();
    }
    private function checkRights(){
        $mr = $this->add('Model_User_Right');
        if (in_array('can_see_'.$this->table.'s', Model_User_Right::$available_rights)){
            $mr->addCondition('user_id', $this->app->currentUser()->get('id'));
            $mr->addCondition('right','LIKE','%can_see_'.$this->table.'s%');
            $mr->tryLoadAny();
            if(!$mr->loaded()){
                $this->addCondition('id',0);
            }
        }

        return $this;
    }
//    function set($name,$value=UNDEFINED) {
//        if( !$this->canManageUsers() ){
//            throw $this->exception('You cannot edit users\' rights');
//        }
//        if ($this->_set) {
//            parent::set($name,$value);
//            return $this;
//        }
//        throw $this->exception('This method is private in this model');
//    }
}
