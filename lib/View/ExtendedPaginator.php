<?php
class View_ExtendedPaginator extends View {
    function init(){
        parent::init();

        if($_GET['paginatorcount']){
            $default_count=$_GET['paginatorcount'];
            setcookie('paginatorcount',$default_count,time()+60*60*24*30*6);
        }elseif ( (isset($_COOKIE['paginatorcount'])) && ($_COOKIE['paginatorcount']>0) ){
            $default_count=$_COOKIE['paginatorcount'];
        }else{
            $default_count=10;
            setcookie('paginatorcount',$default_count,time()+60*60*24*30*6);
/*
            setcookie(
                'paginatorcount',
                $default_count,
                time()+60*60*24*7*30*12,
                $this->api->url('/')->useAbsoluteUrl()
            );
*/
        }

        $v=$this->add('View')->setClass('paginator_count');
        $html='<ul>';
        foreach($this->values as $value){
            if ($default_count==$value){
                $class="active";
            }else{
                $class="";
            }
            $html.='<li class="'.$class.'"><a href="'.$this->api->url(null,array('paginatorcount'=>$value)).'">'.$value.'</a></li>';
        }
        $html.='</ul>';

        $v->setHtml($html);

        $this->grid->addPaginator($default_count);
    }
}
