<?php
/*
 * I take no credit for this!  This was directly pulled from here:
 *
 * http://framework.zend.com/manual/en/zend.json.server.html
 *
 * With a few modifications to make it functional for my example
 * purposes.
 */

/* Change this to wherever your Zend is installed */
require_once('Zend/Json/Server.php');
	
/**
 * Calculator - sample class to expose via JSON-RPC
 */
class Calculator
{
    /**
     * Return sum of two variables
     *
     * @param  int $x
     * @param  int $y
     * @return int
     */
    public function add($x, $y)
    {
        return $x + $y;
    }

    /**
     * Return difference of two variables
     *
     * @param  int $x
     * @param  int $y
     * @return int
     */
    public function subtract($x, $y)
    {
        return $x - $y;
    }

    /**
     * Return product of two variables
     *
     * @param  int $x
     * @param  int $y
     * @return int
     */
    public function multiply($x, $y)
    {
        return $x * $y;
    }

    /**
     * Return the division of two variables
     *
     * @param  int $x
     * @param  int $y
     * @return float
     */
    public function divide($x, $y)
    {
        return $x / $y;
    }
	
	/**
	 * This hangs for a given number of seconds and is for testing async
	 * calls to make sure it doesn't lock up the browser.
	 *
	 * @param int $sleepTime
	 * @return boolean
	 */
	public function hang($sleepTime)
	{
		sleep($sleepTime);
		return true;
	}
	
	/**
	 * This explodes the server (throws exception)
	 */
	public function explode() {
		throw new Exception('BOOM');
	}

	/* Takes an associative array (javascript object).  Returns true
	 * if its able to unpack.
	 *
	 * @param array
	 * @return boolean
	 */
	public function arrayTest(array $arr) {
		if(($arr["hi"] == 1) && ($arr["there"] == 2)) {
			return true;
		}

		return false;
	}
}

$server = new Zend_Json_Server();
$server->setClass('Calculator');

if ('GET' == $_SERVER['REQUEST_METHOD']) {
	// Hang if we're asked to
	if($_REQUEST['hang']) {
		sleep((int)$_REQUEST['hang']);
	}

    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('rpc.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

    // Grab the SMD
    $smd = $server->getServiceMap();

    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}

try {
	$server->handle();
} catch(Exception $e) {
	$err = new Zend_Json_Server_Error($e->getMessage());
	echo $err;
}
