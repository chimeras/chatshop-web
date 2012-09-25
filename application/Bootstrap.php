<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration as ORM_Configuration;
use Doctrine\ORM\EntityManager;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initConfig()
    {
	$config = new Zend_Config($this->getOptions(), true);
	Zend_Registry::set('config', $config);
	return $config;
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

    /*  protected function _initResourceAutoloader()
      {
      $autoloader = new Zend_Loader_Autoloader_Resource(array(
      'basePath' => APPLICATION_PATH,
      'namespace' => 'Application',
      ));

      return $autoloader;
      } */

    protected function _initDoctrine()
    {
	$this->getApplication()
		->getAutoloader()
		/* ->registerNamespace('Zend') */
		->registerNamespace('Bigbek')
		/* ->setFallbackAutoloader(true) */
		->registerNamespace('Doctrine')
		->pushAutoloader(array('doctrine-dbal', 'Doctrine'), 'autoload');
	/* ->pushAutoloader(array('Doctrine', 'autoload'))
	  ->pushAutoloader(array('Doctrine', 'modelsAutoload'), ''); */
	/*
	  ->suppressNotFoundWarnings(true) */

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



	if (APPLICATION_ENV == "development") {
	    $cache = new \Doctrine\Common\Cache\ArrayCache;
	} else {
	    $cache = new \Doctrine\Common\Cache\ApcCache;
	}

	$ormConfig = new ORM_Configuration();
	$ormConfig->setMetadataCacheImpl($cache);
	$driverImpl = $ormConfig->newDefaultAnnotationDriver(APPLICATION_PATH . '/../library/Entities');
	$ormConfig->setMetadataDriverImpl($driverImpl);
	$ormConfig->setQueryCacheImpl($cache);
	$ormConfig->setProxyDir(APPLICATION_PATH . '/../library/Proxies');
	$ormConfig->setProxyNamespace('Bigbek\Proxies');
	$em = EntityManager::create($connectionParams, $ormConfig);
	Zend_Registry::set("em", $em);
    }

}