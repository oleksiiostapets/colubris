<?
class Model_PivotalStory extends Model_Table {
    public $table='pivotal_story';

    function init(){
        parent::init();

        $this->hasOne('Task');
        $this->addField('pivo_project_id');
        $this->addField('pivo_story_id');
        $this->addField('updated_at');

	}
}
