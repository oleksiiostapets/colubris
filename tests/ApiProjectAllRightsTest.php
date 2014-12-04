<?php
class ApiProjectAllRightsTest extends PHPUnit_Framework_TestCase {

    use Trait_Temp_Post;
    use Trait_Temp_Proxy;

    public function testAddApp()
    {
        $app = new App_CLI();
        $app->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib','../lib'),
            'mail'=>array('templates/mail'),
        ))->setBasePath('.');
        $app->dbConnect();
        $app->page = '';
        $app->add('Auth')
            ->usePasswordEncryption('md5')
            ->setModel('Model_User', 'email', 'password')
        ;
        $app->is_test_app = true;
        return $app;
    }

    /**
     * @depends testAddApp
     */
    public function testCreateUser(App_CLI $app)
    {
        $this->app = $app;

        $user_hash = time();
        $m = $app->add('Model_Mock_User');
        $m
            ->set('name','TestUser_'.$user_hash)
            ->set('email','tu_'.$user_hash)
            ->set('password','123123')
            ->save()
        ;
        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     */
    public function testCreatePermissions(App_CLI $app, Model_User $user)
    {
        $this->app = $app;
        $this->user = $user;

        $m = $app->add('Model_Mock_User_Right');
        $m->_set = true;
        $m
            ->set('user_id',$this->user['id'])
            ->set('right','can_see_projects,can_add_projects,can_edit_projects,can_delete_projects')
            ->save()
        ;

        return $m;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     */
    public function testApiLogin(App_CLI $app, Model_User $user)
    {
        $this->app = $app;

        $url = 'v1/auth/login/';
        $data = array('u'=>$user['email'],'p'=>'123123');
        $res = json_decode($this->do_post_request($url,$data));

        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     */
    public function testCreateProject(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $res_success
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/project/saveParams&lhash='.$res_success->hash->lhash;
        $data = ['name' => 'TestProject'.$hash];
        $res = json_decode($this->do_post_request($url,$data));
        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testApiLogin
     * @depends testCreateProject
     */
    public function testGetProject(
        App_CLI $app, $user_res, $result
    ) {
        $this->app = $app;
        $url = 'v1/project/getById&id='.$result->data->id.'&lhash='.$user_res->hash->lhash;
        $res = json_decode($this->do_get_request($url));
        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testGetProject
     */
    public function testUpdateProject(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_res, $project_res
    ) {
        $this->app = $app;

        $hash = time();
        $url = 'v1/project/saveParams&id='.$project_res->data[0]->id.'&lhash='.$user_res->hash->lhash;
        $data = ['name' => 'TestProject'.$hash.'Updated'];
        $res = json_decode($this->do_post_request($url,$data));
        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testCreatePermissions
     * @depends testApiLogin
     * @depends testGetProject
     */
    public function testDeleteProject(
        App_CLI $app, Model_User $user, Model_User_Right $rights, $user_res, $project_res
    ) {
        $this->app = $app;

        $url = 'v1/project/deleteById&id='.$project_res->data[0]->id.'&lhash='.$user_res->hash->lhash;
        $res = json_decode($this->do_get_request($url));
        return $res;
    }

    /**
     * @depends testAddApp
     * @depends testCreateUser
     * @depends testGetProject
     * @depends testUpdateProject
     * @depends testDeleteProject
     * @depends testCreatePermissions
     *
     * @ expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function testAssumptions(
        App_CLI $app, Model_User $user, $project_get_res, $project_update_res, $project_del_res, Model_Mock_User_Right $right
    ) {
        $this->app = $app;
        $this->cleanDB($user,$project_get_res, $right);

        $this->assertEquals($project_get_res->result,'success');
        $this->assertEquals($project_update_res->result,'success');
        $this->assertEquals($project_del_res->result,'success');
    }

    private function cleanDB(Model_User $user, $project_res, Model_Mock_User_Right $right) {
        $project_id = $project_res->data[0]->id;

        $this->app->add('Model_Project')->load($project_id)->forceDelete();
        $user->forceDelete();
        $right->delete();
    }

}




/*


        try {
            $user->forceDelete();
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getFile()."\n";
            echo $e->getLine()."\n";
            echo $e->getTraceAsString();

        }


 */