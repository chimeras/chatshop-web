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
    protected $conn;

    public function __construct()
    {
	$this->conn = Zend_Registry::get('conn');
	$this->em = Zend_Registry::get('em');
    }

}