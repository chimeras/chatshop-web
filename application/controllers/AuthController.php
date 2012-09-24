<?php

use Facebook\User as Facebook_User;
class AuthController extends Zend_Controller_Action
{

    public function indexAction()
    {
	$server = new Zend_Json_Server();
	$server->setClass('Bigbek\Webservice\Authentication');

	if ('GET' == $_SERVER['REQUEST_METHOD']) {
	    // Indicate the URL endpoint, and the JSON-RPC version used:
	    $server->setTarget('/json-rpc.php')
		    ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

	    // Grab the SMD
	    $smd = $server->getServiceMap();

	    // Return the SMD to the client
	    header('Content-Type: application/json');
	    echo $smd;
	    return;
	}

	$server->handle();
    }

}