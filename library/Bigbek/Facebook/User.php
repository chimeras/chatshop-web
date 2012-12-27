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
        $httpClient = new \Zend_Http_Client($this->_fbUrl . 'me/?access_token='
                . $this->_access_token
                .'&fields=id,name,friends.fields(installed,first_name,last_name,gender,picture.width(200).height(200))'.$limitStr,
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

    public function sharePost($message, $picture, $link, $name)
    {
        $httpClient = new \Zend_Http_Client($this->_fbUrl . 'me/feed?access_token=' . $this->_access_token,
            array(
                'maxredirects' => 0,
                'timeout' => 30));
        $httpClient->setParameterPost('message', $message);
        $httpClient->setParameterPost('picture', $picture);
        $httpClient->setParameterPost('link', $link);
        $httpClient->setParameterPost('caption', $name);
        $httpClient->setParameterPost('name', $name);
        $responseBody = $httpClient->request(\Zend_Http_Client::POST)->getBody();
        $responseBodyArray = \Zend_Json::decode($responseBody);
        if (isset($responseBodyArray['id'])) {
            return $responseBodyArray['id'];
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