<?php

use Doctrine\DBAL\DriverManager;
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions(), true);
		Zend_Registry::set('config', $config);
		return $config;
	}

	protected function _initDoctrine()
	{
		$this->getApplication()
				->getAutoloader()
				->registerNamespace('Doctrine')
				->pushAutoloader(array('Doctrine', 'autoload'))
				->pushAutoloader(array('Doctrine', 'modelsAutoload'), '');

		$dConfig = new Doctrine\DBAL\Configuration;
		$config = new Zend_Config($this->getOptions(), true);
		$connectionParams = array(
			'dbname' => $config->resources->db->params->dbname,
			'user' => $config->resources->db->params->username,
			'password' => $config->resources->db->params->password,
			'host' => $config->resources->db->params->host,
			'driver' => $config->resources->db->adapter,
		);

		$conn = DriverManager::getConnection($connectionParams, $dConfig);
		Zend_Registry::set("conn", $conn);
	}

	protected function _initSession()
	{
		$defaultNamespace = new Zend_Session_Namespace();

		if (!isset($defaultNamespace->initialized)) {
			Zend_Session::regenerateId();
			$defaultNamespace->initialized = true;
		}
	}

	protected function _initLogging()
	{

		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('/tmp/shop-brag-app.log');
		$logger->addWriter($writer);
		$logger->registerErrorHandler();
		$session = new Zend_Session_Namespace();
		$session->logger = $logger;
	}

}

