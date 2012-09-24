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
	    echo $row['first_name'] .'<hr />';
	}
	
	$fb = new Facebook_User('557851190','AAACEdEose0cBABniW0wc01mzZBLCGBKlSbY15MBUZBtrSzEGfL2KeXkZCjhNMYtaIWmKBWvjyK4eKBtC9Pv9JG3VHYd9NrfU0YZAz5FUXQZDZD');
	var_dump($fb->getFriends());
    }
}