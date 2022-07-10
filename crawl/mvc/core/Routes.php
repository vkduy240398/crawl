<?php 

class Routes{
    function handleUrl($url){
        global $Routes;
        $url_Return = '';
        $url = trim($url,'/');
        foreach($Routes as $key => $val){
            if (preg_match('~'.$key.'~',$url)) {
               $url_Return = preg_replace('~'.$key.'~',$val,$url);
            }
        }
        return $url_Return;
    }
}