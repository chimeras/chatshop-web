<?php
use Bigbek\Facebook\User as Facebook_User;
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
	/* Initialize action controller here */
	$this->_conn = Zend_Registry::get('conn');
    }

    public function indexAction()
    {
	$sql = "SELECT * FROM user";
	$stmt = $this->_conn->query($sql);
	while ($row = $stmt->fetch()) {
	  //  echo $row['first_name'] .'<hr />';
	}
	
	$fb = new Facebook_User('557851190','AAACEdEose0cBAPJEg6fDmZC1DNRoX8IzA5vcXfZCtJ4vxfFEQPvcXaZCUOQoGzn0ySm8LXRycsTlAljhgdjwU9QApa5YNzzii9dv2flkwZDZD');
	$this->_helper->json($fb->getFriends());
    }
}