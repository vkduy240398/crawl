<?php 
class App{
    protected $controller = 'home';
    protected $action ='index';
    protected $params = [];
    protected $Routes__;
    function __construct(){
        $array = $this->urlProcess();
        $url_check = '';
        $array = array_values($array);
        foreach($array as $key => $val){
            $url_check .= $val.'/';
            $file_check = trim($url_check,'/');
            $fileArray = explode('/', $file_check);
            $file_check = implode('/',$fileArray);
            if (!empty($array[$key - 1])) {
               unset($array[$key - 1]);
            }
            if (file_exists('./mvc/controllers/'.$file_check.'.php')) {
               $url_check = $file_check;
               break;
            }
        }
        $array = array_values($array);
        if ($array != NULL) {
           
            if (file_exists('./mvc/controllers/'.$url_check.'.php')) {
                $this->controller = $array[0];
                require_once './mvc/controllers/'.$url_check.'.php';
                if (class_exists($this->controller)) {
                    $this->controller = new $this->controller;
                    unset($array[0]);
                }
            }
            else{
                require_once './mvc/controllers/'.$this->controller.'.php';
                $this->controller = new $this->controller;
            }
        }
        if (isset($array[1])) {
            if (method_exists($this->controller, $array[1])) {
                $this->action = $array[1];
                unset($array[1]);
            }
        }
        $this->params = $array?array_values($array):[];
        call_user_func_array([$this->controller,$this->action], $this->params);
    }
    function getUrl(){
        $url = '';
        if (!empty($_SERVER['PATH_INFO'])) {
           $url = $_SERVER['PATH_INFO'];
        }
        else{
            $url = '/';
        }
        return $url;
    }
    function urlProcess(){
        $this->Routes__ = new Routes();
        $url = $this->getUrl();
        $url_return = $this->Routes__->handleUrl($url);
        return explode("/",filter_var(trim($url_return,"/")));      
    }
}