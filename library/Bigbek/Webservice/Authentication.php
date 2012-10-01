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
	$fb = new Facebook_User($accessToken);
	
	$userFbInfo = $fb->getInfo();
	$userFbInfo['first_name'];
	$userFbInfo['last_name'];
	
	return \Zend_Json::encode(array('session' => 'asdfasdfasdfasdfasdfasdf', 'first_name'=>$userFbInfo['first_name']));
    }

    /**
     * 
     * @param string $session
     * @return boolean
     */
    public function signIn($session)
    {
	//$user = new \Application_Model_User;
	//var_dump($user);
	$user = $this->em->find('Application_Model_User', 1);
	/*$user->firstName = 'Vardan';
	$this->em->flush();*/
	echo $user->getFirstName();
	$userFb = $this->em->find('Application_Model_UserFacebook', 1);
	
	$userFacebooks = $user->getUserFacebooks();
	// var_dump($userFacebooks[0]->getAccessToken());
	 //var_dump($userFb->getAccessToken());
	 var_dump($userFb->getUser()->getFirstName());
	 return;
	  $fb = new Facebook_User($uid, $accessToken);
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
