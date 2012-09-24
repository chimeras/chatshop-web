<?php
namespace Bigbek\Facebook;
class User
{

    protected $_fbUrl = null;
    protected $_uid = null;
    protected $_access_token = null;

    public function __construct($uid, $accessToken = null)
    {
	$config = \Zend_Registry::get('config')->facebook;
	$this->_fbUrl = $config->uri;
	$this->_uid = $uid;
	$this->_access_token = $accessToken;
    }

    public function getFriends()
    {
	$httpClient = new \Zend_Http_Client($this->_fbUrl .$this->_uid . '/friends?access_token=' . $this->_access_token,
			array(
			    'maxredirects' => 0,
			    'timeout' => 30));
	$responseBody = $httpClient->request()->getBody();
	$responseBodyArray = \Zend_Json::decode($responseBody);
	return $responseBodyArray['data'];
    }

}