<?php
class page_testapi extends Page {
    function init() {
        parent::init();

    }

    function page_index() {
        if($_GET['message']) $this->add('View')->set($_GET['message']);
        if($_GET['lhash']) $this->add('View')->setHtml('<a href="'.$this->app->url('testapi/check',array('lhash'=>$_GET['lhash'])).'">Check</a>');

        $f = $this->add('Form');
        $f->addField('u');
        $f->addField('p');
        $f->addSubmit('Send');

        if ($f->isSubmitted()){
            //var_dump($f->get());
            $url = 'http://localhost/colubris43/api/?page=v1/user/login/';
            $data = array('u'=>'oleksii.ostapets@gmail.com','p'=>'123');
            $res = $this->do_post_request($url,$data);
            if($res) {
                $res = json_decode($res);
                $this->js()->redirect($this->app->url('testapi',array('message' => 'user logged in and have got lhash=' . $res->lhash, 'lhash' => $res->lhash)))->execute();
            } else {
                $this->js()->redirect($this->app->url('testapi',array('message' => 'wrong login')));
            }
        }
    }

    function page_check(){
        $url = 'http://localhost/colubris43/api/?page=v1/user/check&lhash='.$_GET['lhash'];
        $data = array('lhash'=>$_GET['lhash']);
        $res = $this->do_get_request($url,$data);

        if($res == 'true') $this->add('View')->set('lhash valid');
        else $this->add('View')->set('lhash invalid');
    }

    function do_post_request($url, $data, $optional_headers = null) {
        $data = http_build_query($data);
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data
        ));

        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        return $response;
    }
    function do_get_request($url, $data, $optional_headers = null) {
        $data = http_build_query($data);
        $params = array('http' => array(
            'method' => 'GET',
            'content' => $data
        ));

        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        return $response;
    }
}
