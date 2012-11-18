<?php

class Application_Model_Reminder extends Application_Model_Db_Row_Reminder
{

	public function fillFrom($data)
	{
		
		$error = array();
		if (!isset($data['shoplistId'])) {
			$error[] = 'shoplistId : shopping list id missed';
		}
		if (!isset($data['name'])) {
			$error[] = 'name : reminder name missed';
		}
		$this->setShoplistId($data['shoplistId']);
		$this->setName($data['name']);
		if (isset($data['description'])) {
			$this->setDescription($data['description']);
		}

		if (isset($data['price'])) {
			$this->setPrice($data['price']);
		}

		if (isset($data['storeName'])) {
			$this->setStoreName($data['storeName']);
		}

		if (isset($data['storeLongitude'])) {
			$this->setStoreLongitude($data['storeLongitude']);
		}

		if (isset($data['storeLattitude'])) {
			$this->setStoreLattitude($data['storeLattitude']);
		}
		if(count($error) > 0){
			return $error;
		}else{
			return TRUE;
		}
	}

}