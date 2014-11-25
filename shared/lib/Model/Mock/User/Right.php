<?php
// The user has all rights
class Model_Mock_User_Right extends Model_User_Right{
    function set($name,$value=undefined){
        if(is_array($name)){
            foreach($name as $key=>$val)$this->set($key,$val);
            return $this;
        }
        if($name===false || $name===null){
            return $this->reset();
        }

        // Verify if such a filed exists
        if($this->strict_fields && !$this->hasElement($name))throw $this->exception('No such field','Logic')
            ->addMoreInfo('name',$name);

        if($value!==undefined
            && (
                is_object($value)
                || is_object($this->data[$name])
                || is_array($value)
                || is_array($this->data[$name])
                || (string)$value!=(string)$this->data[$name] // this is not nice..
                || $value !== $this->data[$name] // considers case where value = false and data[$name] = null
                || !isset($this->data[$name]) // considers case where data[$name] is not initialized at all (for example in model using array controller)
            )
        ) {
            $this->data[$name]=$value;
            $this->setDirty($name);
        }
        return $this;
    }

}