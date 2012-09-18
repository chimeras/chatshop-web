<?php

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
    echo $row['first_name'];
}
	}

}

