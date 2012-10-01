<?php
class Application_Model_UserFacebook extends Application_Model_Db_Row_UserFacebook
{
	/**
	 * 
	 * @return Application_Model_User
	 */
	public function getUser()
	{
		return $this->findParentRow('Application_Model_Users');
	}

}