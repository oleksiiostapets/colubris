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
            //$url = 'http://localhost/colubris43/api/?page=v1/user/login/';
            $absolute_url = $this->full_url($_SERVER);
            $url = $absolute_url.'/api/?page=v1/user/login/';
            $data = array('u'=>$f->get('u'),'p'=>$f->get('p'));
            $res = $this->do_post_request($url,$data);
            if($res == 'false') {
                $this->js()->redirect($this->app->url('testapi',array('message' => 'wrong login')))->execute();
            } else {
                $res = json_decode($res);
                $this->js()->redirect($this->app->url('testapi',array('message' => 'user logged in and have got lhash=' . $res->lhash, 'lhash' => $res->lhash)))->execute();
            }
        }
    }
    function url_origin($s, $use_forwarded_host=false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }
    function full_url($s, $use_forwarded_host=false)
    {
        return $this->url_origin($s, $use_forwarded_host) . substr($s['REQUEST_URI'],0,strpos($s['REQUEST_URI'],'/public'));
    }
    function page_check(){
        //$url = 'http://localhost/colubris43/api/?page=v1/user/check&lhash='.$_GET['lhash'];
        $absolute_url = $this->full_url($_SERVER);
        $url = $absolute_url.'/api/?page=v1/user/check&lhash='.$_GET['lhash'];
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
