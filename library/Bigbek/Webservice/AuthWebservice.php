<?php

namespace Bigbek\Webservice;

use Bigbek\Facebook\User as Facebook_User;

/**
 * Authentication handler class
 *
 * @author vahram
 */
class AuthWebservice extends BaseWebservice
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
		if (time() < $expirationDate) {
			$fb = new Facebook_User($accessToken);

			$userFbInfo = $fb->getInfo();

			$userFacebooks = new \Application_Model_UserFacebooks();
			$userFb = $userFacebooks->fetch($userFbInfo['id']);
			if (is_object($userFb)) {
				$user = $userFb->getUser();
				if(!is_object($user)){
					return \Zend_Json::encode(array('session' => null, 'message' => 'no valid user'));
				}
				$session = $user->getSession();
			} else {
				
				$session = \Zend_Session::getId();
				\Zend_Session::destroy();
				$users = new \Application_Model_Users;
				$user = $users->fetchNew();
				$user->setFirstName($userFbInfo['first_name']);
				$user->setLastName($userFbInfo['last_name']);
				$user->setSession($session);
				$user->save();
				$userFb = $userFacebooks->fetchNew();
				$userFb->setId($userFbInfo['id']);
				$userFb->setUserId($user->getId());
				$userFb->setAccessToken($accessToken);
				$userFb->setExpirationDate(date("Y-m-d H:i:s", $expirationDate));
				$userFb->save();
			}
			\Zend_Session::destroy();
			return \Zend_Json::encode(array('user' => $user->toArray()));
		} else {
			return \Zend_Json::encode(array('session' => null, 'message' => 'access token expired'));
		}
	}

	/**
	 * 
	 * @param string $session
	 * @return boolean
	 */
	public function signIn($session)
	{
		$users = new \Application_Model_Users;
		$user = $users->fetchBySession($session);
		if (is_object($user)) {
			return \Zend_Json::encode(array('user' => $user->toArray()));
		} else {
			return \Zend_Json::encode(array('error' => 'wrong session'));
		}
	}
}
