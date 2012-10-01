<?php

namespace Bigbek\Webservice;

use \Zend_Registry;

/**
 * Base class for webservice models
 *
 * @author vahram
 */
class BaseWebservice
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @var EntityManager
     */
    public function __construct()
    {
	$registry = Zend_Registry::getInstance();
	$this->em = $registry->entitymanager;
    }

}