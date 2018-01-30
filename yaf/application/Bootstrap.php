<?php
class Bootstrap extends Yaf_Bootstrap_Abstract{
    public function _initCommonFunction(){
        Yaf_Loader::import(Yaf_Application::app()->getConfig()->application->directory.'/common/function.php');
    }
}