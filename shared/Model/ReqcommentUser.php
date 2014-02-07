<?
class Model_ReqcommentUser extends Model_Table {
    public $table='reqcomment_user';
    function init(){
        parent::init();
        $this->hasOne('Reqcomment');
        $this->hasOne('User');
    }
}