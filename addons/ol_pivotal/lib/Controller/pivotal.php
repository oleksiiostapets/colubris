<?php
namespace ol_pivotal;
require_once __DIR__.'/../../vendor/pivotal/pivotal.php';
class Controller_pivotal extends \AbstractController{
    public $objPivotal;
    public $states=array(
        'unscheduled'=>'unstarted',
        'unstarted'=>'unstarted',
        'started'=>'started',
        'finished'=>'finished',
        'delivered'=>'tested',
        'rejected'=>'rejected',
        'accepted'=>'accepted'
    );
    public $types=array(
        'feature'=>'change request',
        'bug'=>'bug',
        'chore'=>'change request',
        'release'=>'change request'
    );
    public $users;
    function init(){
        parent::init();

        // add add-on locations to pathfinder
        $l = $this->api->locate('addons',__NAMESPACE__,'location');
        $addon_location = $this->api->locate('addons',__NAMESPACE__);
        $this->api->pathfinder->addLocation($addon_location,array(
            'php'=>array('lib','vendor')
        ))->setParent($l);

        $this->objPivotal = new \pivotal;
        $this->objPivotal->token = $this->api->getConfig("pivotal/token");
        $this->users = $this->api->getConfig("pivotal/users");
    }
    function getStories($project_id){
        $stories = $this->objPivotal->getStories($project_id);
        return $stories;

        echo "<pre>";
        print_r($stories);
        echo "</pre>";
    }
    function importStories($project_id){
        $conf_projects_ids=$this->api->getConfig("pivotal/projects");
        foreach($conf_projects_ids as $pivo_project_id => $col_project_id){
            if($project_id==$pivo_project_id){
                $stories=$this->getStories($pivo_project_id);
                foreach($stories as $story){
                    $pivotal_story=$this->add('Model_PivotalStory');
                    $pivotal_story->tryLoadBy('pivo_story_id',$story->id);
                    if(!$pivotal_story->loaded()){
                        $task=$this->add('Model_Task');
                        $task->set('name',(string)$story->name);
                        $states=$this->states;
                        $task->set('status',$states[(string)$story->current_state]);
                        $types=$this->types;
                        $task->set('type',$types[(string)$story->story_type]);
                        $task->set('descr_original',(string)$story->description);
                        $task->set('project_id',$col_project_id);
                        $users=$this->users;
                        if($story->requested_by!=''){
                            $task->set('requester_id',$users[(string)$story->requested_by]);
                        }
                        if($story->owned_by!=''){
                            $task->set('assigned_id',$users[(string)$story->owned_by]);
                        }
                        $task->save();

                        $pivotal_story->set('task_id',$task['id']);
                        $pivotal_story->set('pivo_project_id',$pivo_project_id);
                        $pivotal_story->set('pivo_story_id',$story->id);
                        $pivotal_story->set('updated_at',(string)$story->updated_at);
                        $pivotal_story->save();
                    }else{
                        if($pivotal_story->get('updated_at')!=(string)$story->updated_at){
                            $task=$this->add('Model_Task')->load($pivotal_story['task_id']);
                            $task->set('name',(string)$story->name);
                            $states=$this->states;
                            $task->set('status',$states[(string)$story->current_state]);
                            $types=$this->types;
                            $task->set('type',$types[(string)$story->story_type]);
                            $task->set('descr_original',(string)$story->description);
                            $task->set('project_id',$col_project_id);
                            $users=$this->users;
                            if($story->requested_by!=''){
                                $task->set('requester_id',$users[(string)$story->requested_by]);
                            }
                            if($story->owned_by!=''){
                                $task->set('assigned_id',$users[(string)$story->owned_by]);
                            }
                            $task->save();

                            $pivotal_story->set('updated_at',(string)$story->updated_at);
                            $pivotal_story->save();
                        }
                    }
                }
            }
        }
    }
}