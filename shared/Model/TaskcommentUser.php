<?
class Model_TaskcommentUser extends Model_Table {
    public $table='taskcomment_user';
    function init(){
        parent::init();
        $this->hasOne('Taskcomment');
        $this->hasOne('User');
    }
}