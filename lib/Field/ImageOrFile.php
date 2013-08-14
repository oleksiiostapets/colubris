<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/14/13
 * Time: 7:27 PM
 * To change this template use File | Settings | File Templates.
 */
class Field_ImageOrFile extends filestore\Field_File {

    /* Adds a calculated field for displaying a thubnail of this image */
    function addThumb($name=null,$thumb='thumb_url'){

        if(!$name)$name=$this->getDereferenced().'_thumb';

        $self=$this;
        $this->owner->addExpression($name)->set(function($m)use($self,$thumb){
            return $m->refSQL($self->short_name)->fieldQuery($thumb);
        });
        return $this;
    }
}