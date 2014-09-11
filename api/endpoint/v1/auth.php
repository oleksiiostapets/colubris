<?php
class endpoint_v1_auth extends Endpoint_REST {

    use Helper_Url;

    function init() {
        parent::init();
    }


    function post_login() {
        if(!$this->app->auth->verifyCredentials($_POST['u'],$_POST['p'])) {
            return [
                'result' => 'error',
                'message'   => 'Wrong password'
            ];
        } else {
            $u = $this->add('Model_User')->tryLoadBy('email',$_POST['u']);
            if($u->loaded()){
                $h_check = null;
                if($u->get('lhash') != null) {
                    $h_check = $this->add('Model_User')->checkUserByLHash($u->get('lhash'));
                }
                if($h_check){
                    $res = $u->prolongLHash();
                }else{
                    $res = $u->setLHash();
                }

                return [
                    'result' => 'success',
                    'hash'   => $res,
                    'message' => 'User found'
                ];
            } else {
                return [
                    'result' => 'error',
                    'message'   => 'User not found after check'
                ];
            }

            return [
                'result' => 'error',
                'message'   => 'Unexpected error'
            ];
        }
    }

    function post_logout(){
        $lhash = $this->checkPostParameter('lhash');
        $u = $this->add('Model_User')->tryLoadBy('lhash', $lhash);
        if ($u->loaded()){
            $u->set('lhash',null);
            $u->save();
        }
        return [
            'result' => 'success'
        ];
    }

    function get_check(){
        $u = $this->add('Model_User');
        $res = $u->checkUserByLHash($_GET['lhash']);

        if(!$res){
            return [
                'result' => 'error',
                'message'   => 'User not found'
            ];
        }else{
            return [
                'result' => 'success',
                'user'   => $res->get(),
                'message' => 'User found'
            ];
        }

    }
}

//function post_login() {
//    var_dump($_POST);
//
//    if(!$this->app->auth->verifyCredentials($_POST['u'],$_POST['p'])) {
//        return [
//            'result' => 'error',
//            'info'   => 'User not found'
//        ];
//    } else {
//        $u = $this->add('Model_User')->tryLoadBy('email',$_POST['u']);
//        if($u->loaded()){
//            $res = $u->setLHash();
//            return [
//                'result' => 'success',
//                'hash'   => $res,
//            ];
//        } else {
//            return [
//                'result' => 'error',
//                'info'   => 'User not found after check'
//            ];
//        }
//        //$res = $u->checkUserByLHash('72ffa947a251bec0e71887ad689a2bcf');
//
//        return [
//            'result' => 'error',
//            'info'   => 'Unexpected error'
//        ];
//    }
//}