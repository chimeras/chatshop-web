<?php
/*
namespace Bigbek;
use Zend\Http\Client;
use Facebook\Access;*/
class Facebook
{
     /** 
     * @var Bigbek\Facebook\Access 
     **/
    protected $access;
    public function __construct()
    {
	$this->access = new Access;
    }
}