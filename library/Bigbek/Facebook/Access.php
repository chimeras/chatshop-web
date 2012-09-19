<?php
use Zend\Http\Request;

class Access
{
    /** @var Auth **/
    protected $auth;
    /** @var Zend\Http **/
    protected $http;
    
    const FACEBOOK_GRAPH_URI = 'https://graph.facebook.com/';
    
    public function __construct($uid, $authtoken = null)
    {
	$this->http = new Request(FACEBOOK_GRAPH_URI);
	
    }
} 