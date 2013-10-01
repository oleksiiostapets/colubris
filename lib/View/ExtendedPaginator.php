<?php
class View_ExtendedPaginator extends View {
    function init(){
        parent::init();

        if($_GET['paginator_count']){
            $default_count=$_GET['paginator_count'];
            setcookie(
                'paginator_count',
                $default_count,
                time()+60*60*24*7*30*12,
                $this->api->url('/')->useAbsoluteUrl()
            );
        }elseif ( ($_COOKIE['paginator_count']) && ($_COOKIE['paginator_count']>0) ){
            $default_count=$_COOKIE['paginator_count'];
        }else{
            $default_count=10;
            setcookie('paginator_count',$default_count, 60*60*24*7*30*12,'/');
        }

        $v=$this->add('View')->setClass('paginator_count');
        $html='<ul>';
        foreach($this->values as $value){
            if ($default_count==$value){
                $class="active";
            }else{
                $class="";
            }
            $html.='<li class="'.$class.'"><a href="'.$this->api->url(null,array('paginator_count'=>$value)).'">'.$value.'</a></li>';
        }
        $html.='</ul>';

        $v->setHtml($html);

        $this->grid->addPaginator($default_count);
    }
}
