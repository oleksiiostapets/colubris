<?php
class Mymenu extends Menu_Basic {
	function isCurrent($href){
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);

		if ( (substr($href,0,7)=='manager' && substr($this->api->page,0,7)=='manager') ) { return true; }
		if ( (substr($href,0,5)=='admin' && substr($this->api->page,0,5)=='admin') ) { return true; }
		if ( (substr($href,0,4)=='team' && substr($this->api->page,0,4)=='team') ) { return true; }
		if ( (substr($href,0,6)=='client' && substr($this->api->page,0,6)=='client') ) { return true; }
		
		return $href==$this->api->page||$href==';'.$this->api->page||$href.$this->api->getConfig('url_postfix','')==$this->api->page;
	}
}