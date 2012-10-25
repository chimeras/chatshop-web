<?php

 
class App_View_Helper_FullUrl extends Zend_View_Helper_Abstract {

public function fullUrl($url) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $url = $request->getScheme() . "://" . $request->getHttpHost(). "/" . $url;
    return $url;
  }
}