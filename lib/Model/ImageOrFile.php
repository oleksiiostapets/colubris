<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 8/14/13
 * Time: 5:30 PM
 * To change this template use File | Settings | File Templates.
 */
class Model_ImageOrFile extends filestore\Model_File {

    public $default_thumb_width=140;
    public $default_thumb_height=140;
    public $entity_file='File';

    protected $image_mime_type = false;

    function init() {
        parent::init();

        $this->i=$this->join('filestore_image.original_file_id');

        /*
        $this->hasOne('filestore/'.$this->entity_file,'original_file_id')
            ->caption('Original File');
         */

        $this->i->hasOne('filestore/'.$this->entity_file,'thumb_file_id')
            ->caption('Thumbnail');

        $this->addExpression('thumb_url')->set(array($this,'getThumbURLExpr'));
    }
    function performImport(){
        $this->image_mime_type = $this->isImage($this->import_source);
        parent::performImport();
        if ($this->image_mime_type) $this->createThumbnails();
        return $this;
    }
    public $images_types = array( 'image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp' );
    function isImage($path) {
        if(!$path) throw $this->exception('Import file');

        if(!function_exists('finfo_open')) throw $this->exception('You have to enable php_fileinfo extension of PHP.');
        $finfo = finfo_open(FILEINFO_MIME_TYPE, $this->magic_file);
        if($finfo===false)throw $this->exception("Can't find magic_file in finfo_open().")
            ->addMoreInfo('Magic_file: ',isnull($this->magic_file)?'default':$this->magic_file);
        $mime_type = finfo_file($finfo, $path);
        finfo_close($finfo);

        if (in_array($mime_type, $this->images_types)) {
            return $mime_type;
        }
        return false;
    }

    /* *****************************
     *
     *   methods from Model_Image
     *   keep this part up to date manually
     *
     */

    /* Produces expression which calculates full URL of image */
    function getThumbURLExpr($m,$q){
        $m=$this->add('filestore/Model_'.$this->entity_file);
        $m->addCondition('id',$this->i->fieldExpr('thumb_file_id'));
        return $m->fieldQuery('url');
    }


    function createThumbnails(){
        if($this->id)$this->load($this->id);// temporary
        $this->createThumbnail('thumb_file_id',$this->default_thumb_width,$this->default_thumb_height);
    }
    function imagickCrop($i,$width,$height){
        $geo = $i->getImageGeometry();

        if($geo['width']<$width && $geo['height']<$height)return; // don't crop, image is too small

        // crop the image
        if(($geo['width']/$width) < ($geo['height']/$height))
        {
            $i->cropImage($geo['width'], floor($height*$geo['width']/$width), 0, (($geo['height']-($height*$geo['width']/$width))/2));
        }
        else
        {
            $i->cropImage(ceil($width*$geo['height']/$height), $geo['height'], (($geo['width']-($width*$geo['height']/$height))/2), 0);
        }
        // thumbnail the image
        $i->ThumbnailImage($width,$height,true);
    }
    function createThumbnail($field,$x,$y){
        // Create entry for thumbnail.
        $thumb=$this->ref($field,'link');
        if(!$thumb->loaded()){
            $thumb->set('filestore_volume_id',$this->get('filestore_volume_id'));
            $thumb->set('original_filename','thumb_'.$this->get('original_filename'));
            $thumb->set('filestore_type_id',$this->get('filestore_type_id'));
            $thumb['filename']=$thumb->generateFilename();
        }

        if(class_exists('\Imagick',false)){
            $image=new \Imagick($this->getPath());
            //$image->resizeImage($x,$y,\Imagick::FILTER_LANCZOS,1,true);
            //$image->cropThumbnailImage($x,$y);
            $this->imagickCrop($image,$x,$y);
            $this->hook("beforeThumbSave", array($thumb));
            $image->writeImage($thumb->getPath());
            $thumb["filesize"] = filesize($thumb->getPath());
        }elseif(function_exists('imagecreatefromjpeg')){
            list($width, $height, $type) = getimagesize($this->getPath());
            ini_set("memory_limit","1000M");


            $a=array(null,'gif','jpeg','png');
            $type=@$a[$type];
            if(!$type)throw $this->exception('This file type is not supported');

            //saving the image into memory (for manipulation with GD Library)
            $fx="imagecreatefrom".$type;
            $myImage = $fx($this->getPath());

            $thumbSize = $x;    // only supports rectangles
            if($x!=$y && 0)throw $this->exception('Model_Image currently does not support non-rectangle thumbnails with GD extension')
                ->addMoreInfo('x',$x)
                ->addMoreInfo('y',$y);

            // calculating the part of the image to use for thumbnail
            if ($width > $height) {
                $y = 0;
                $x = ($width - $height) / 2;
                $smallestSide = $height;
            } else {
                $x = 0;
                $y = ($height - $width) / 2;
                $smallestSide = $width;
            }

            // copying the part into thumbnail
            $myThumb = imagecreatetruecolor($thumbSize, $thumbSize);
            imagecopyresampled($myThumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

            //final output
            imagejpeg($myThumb, $thumb->getPath());
            imageDestroy($myThumb);
            imageDestroy($myImage);
            $thumb["filesize"] = filesize($thumb->getPath());
        }else{
            // No Imagemagick support. Ignore resize
            $thumb->import($this->getPath(),'copy');
        }
        $thumb->save();  // update size and chmod
    }
}