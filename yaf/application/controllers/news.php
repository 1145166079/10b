<?php
class NewsController extends ApiController{
    public function signAction(){
        if($this->api($_GET)){
            echo $_GET['callback']."(".json_encode(array(1)).")";
            die;
        }else{
            echo $_GET['callback']."(".json_encode(array(2)).")";
            die;
        }
    }
}