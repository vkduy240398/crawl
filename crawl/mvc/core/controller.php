<?php 
class controller{
    function models($models){
        require_once "./mvc/models/".$models.'.php';
        return new $models;
    }
    function view($view, $data = []){
        foreach($data as $key => $val){
            $$key = $val;
        }
        require_once "./mvc/views/".$view.'.php';
    }
}