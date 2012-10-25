<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initBigbek()
	{
		$this->getApplication()
				->getAutoloader()
				->registerNamespace('Bigbek');
	}

	protected function _initResourceAutoloader()
	{
		$autoloader = new Zend_Loader_Autoloader_Resource(array(
					'basePath' => APPLICATION_PATH,
					'namespace' => 'Application',
				));

		$autoloader->addResourceType('model', 'models', 'Model');
		return $autoloader;
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
		$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../log/app.log');
		$logger->addWriter($writer);
		$logger->registerErrorHandler();
		Zend_Registry::set('logger', $logger);
		return $logger;
	}

	protected function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		
	}

	/* protected function _initDbUpdate()
	  {
	  $resource = $this->getPluginResource('db');
	  $db = $resource->getDbAdapter();
	  Zend_Registry::set("db", $db);
	  $updater = new Guessit_Updater;
	  $options = $this->getOption('resources');
	  $updater->dbUpdate($options);
	  } */

	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions(), true);
		Zend_Registry::set('config', $config);
		return $config;
	}

	public function _initRequest()
	{
		$this->bootstrap('frontController');
		$front = $this->getResource('frontController');
		$front->setRequest(new Zend_Controller_Request_Http());

		$request = $front->getRequest();
		Zend_Registry::set('request', $request);
	}

}