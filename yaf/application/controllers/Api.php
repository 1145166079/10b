<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/1/24
 * Time: 13:18
 */
class ApiController extends Yaf_Controller_Abstract{
    public function api($get){
        $sign=$get['sign'];
        $key='qweqweqe';
        unset($get['sign']);
        unset($get['_']);
        unset($get['key']);
        unset($get['callback']);
        ksort($get);
        $str='';
        foreach ($get as $k=>$v){
            $str.="&".$k.'='.$v;
        }
        $str=substr($str,1);
        $signs=md5($str.$key);
       if($sign==$signs){
           return true;
       }else{
           return false;
       }
    }
}
