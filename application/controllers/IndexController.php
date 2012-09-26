<?php
 
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $registry = Zend_Registry::getInstance();
        $this->_em = $registry->entitymanager;
    }
 
    public function indexAction()
    {
        $testEntity = new Default_Model_User;
        $testEntity->setFirstName('Zaphod Beeblebrox');
        $this->_em->persist($testEntity);
        $this->_em->flush();
    }
 
}