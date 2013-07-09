<?php
class Mysubmenu extends Menu_Basic {
	function isCurrent($href){
		// returns true if item being added is current
		if(!is_object($href))$href=str_replace('/','_',$href);
		
		if ($href==substr($this->api->page,0,strlen($href))) { return true; }
		
		return $href==$this->api->page||$href==';'.$this->api->page||$href.$this->api->getConfig('url_postfix','')==$this->api->page;
	}
}