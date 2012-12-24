<?php
namespace Bigbek\Facebook;
class User
{

    protected $_fbUrl = null;
    protected $_access_token = null;

    public function __construct($accessToken)
    {
        $config = \Zend_Registry::get('config')->facebook;
        $this->_fbUrl = $config->uri;
        $this->_access_token = $accessToken;
    }

    public function getFriends($offset, $limit = 0)
    {
        $limitStr = $limit===0 ? '': '.offset('.$offset.').limit('.$limit.')';
        $httpClient = new \Zend_Http_Client($this->_fbUrl . 'me/?access_token=' . $this->_access_token .'&fields=id,name,friends.fields(installed,first_name,last_name,picture)'.$limitStr,
            array(
                'maxredirects' => 0,
                'timeout' => 30));
        $responseBody = $httpClient->request()->getBody();
        $responseBodyArray = \Zend_Json::decode($responseBody);
        if (isset($responseBodyArray['friends'])) {
            return $responseBodyArray['friends'];
        } else {
            return array('error' => '');
        }
    }

    public function getInfo()
    {
        $httpClient = new \Zend_Http_Client($this->_fbUrl . 'me/?access_token=' . $this->_access_token,
            array(
                'maxredirects' => 0,
                'timeout' => 30));
        $responseBody = $httpClient->request()->getBody();
        $responseBodyArray = \Zend_Json::decode($responseBody);
        if (is_array($responseBodyArray)) {
            return $responseBodyArray;
        } else {
            return array('error' => '');
        }
    }

}