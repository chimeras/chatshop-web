<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * generate registry
     * @return Zend_Registry
     */
    protected function _initRegistry()
    {
	$registry = Zend_Registry::getInstance();
	return $registry;
    }

    /**
     * Register namespace Default_
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
	$autoloader = new Zend_Application_Module_Autoloader(array(
		    'namespace' => 'Default_',
		    'basePath' => dirname(__FILE__),
		));
	return $autoloader;
    }

    protected function _initBigbek()
    {
	$this->getApplication()
		->getAutoloader()
		->registerNamespace('Bigbek');
    }

    /**
     * Initialize Doctrine
     * @return Doctrine_Manager
     */
    public function _initDoctrine()
    {
	// include and register Doctrine's class loader
	require_once('Doctrine/Common/ClassLoader.php');
	$classLoader = new \Doctrine\Common\ClassLoader(
			'Doctrine',
			APPLICATION_PATH . '/../library/'
	);
	$classLoader->register();

	// create the Doctrine configuration
	$config = new \Doctrine\ORM\Configuration();

	// setting the cache ( to ArrayCache. Take a look at
	// the Doctrine manual for different options ! )
	$cache = new \Doctrine\Common\Cache\ArrayCache;
	$config->setMetadataCacheImpl($cache);
	$config->setQueryCacheImpl($cache);

	// choosing the driver for our database schema
	// we'll use annotations
	$driver = $config->newDefaultAnnotationDriver(
		APPLICATION_PATH . '/models'
	);
	$config->setMetadataDriverImpl($driver);

	// set the proxy dir and set some options
	$config->setProxyDir(APPLICATION_PATH . '/models/Proxies');
	$config->setAutoGenerateProxyClasses(true);
	$config->setProxyNamespace('App\Proxies');

	// now create the entity manager and use the connection
	// settings we defined in our application.ini
	$connectionSettings = $this->getOption('doctrine');
	$conn = array(
	    'driver' => $connectionSettings['conn']['driv'],
	    'user' => $connectionSettings['conn']['user'],
	    'password' => $connectionSettings['conn']['pass'],
	    'dbname' => $connectionSettings['conn']['dbname'],
	    'host' => $connectionSettings['conn']['host']
	);
	$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

	// push the entity manager into our registry for later use
	$registry = Zend_Registry::getInstance();
	$registry->entitymanager = $entityManager;

	return $entityManager;
    }

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

}