<?php

namespace Bigbek\Webservice;

use Doctrine\ORM\EntityManager;
use Bigbek\Facebook\User as Facebook_User;

/**
 * Authentication handler class
 *
 * @author vahram
 */
class Authentication extends BaseWebservice
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct()
    {
	parent::__construct();
    }

    /**
     * 
     * @param string $accessToken
     * @param string $expirationDate
     * @return boolean
     */
    public function fbSignUp($accessToken, $expirationDate)
    {
	//$fb = new Facebook_User($uid, $accessToken);
	return \Zend_Json::encode(array('session' => 'asdfasdfasdfasdfasdfasdf'));
	//return true;
    }

    /**
     * 
     * @param string $session
     * @return boolean
     */
    public function signIn($session)
    {

	/* var_dump($this->em->find('User', 1));
	  $fb = new Facebook_User($uid, $accessToken); */
	if ($session == 'asdfasdfasdfasdfasdfasdf') {
	    return \Zend_Json::encode(array('result' => 'success'));
	} else {
	    return \Zend_Json::encode(array('error' => 'wrong session'));
	}
	//return true;
    }

    /**
     * 
     * @param integer $uid
     * @param string $accessToken
     * @return boolean
     */
    public function fbTest($uid, $accessToken)
    {
	$fb = new Facebook_User($uid, $accessToken);
	echo \Zend_Json::encode($fb->getFriends());
	return true;
    }

}
