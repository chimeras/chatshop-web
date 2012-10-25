<?php
class FeedController extends Zend_Controller_Action
{
	
	public function cjHandleTestAction()
	{
		
	}

	public function cjHandleAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		$formData = $this->_request->getPost();
		/* @var $logger Zend_Log */
		$logger = Zend_Registry::get('logger');
		$logger->log(Zend_Debug::dump($formData), Zend_Log::DEBUG); 
	}
}