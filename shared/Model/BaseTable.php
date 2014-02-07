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
}
