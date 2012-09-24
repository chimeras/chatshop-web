<?php
namespace Bigbek\Webservice;
use Bigbek\Facebook\User as Facebook_User;
/**
 * Authentication handler class
 *
 * @author vahram
 */
class Authentication
{
    /**
     * 
     * @param integer $uid
     * @param string $accessToken
     * @return boolean
     */
    public function fbLogin($uid, $accessToken)
    {
	$fb = new Facebook_User('557851190','AAACEdEose0cBADjkPKOjtBaPBltdLcjutUf2QdbBCeySARcdw2WZCF3JRlZA5qiudhiy77VBdliUQBAGolvaPpZAMhKZCvd6RD3IzH4IigZDZD');
	echo \Zend_Json::encode($fb->getFriends());
	return true;
    }
}
