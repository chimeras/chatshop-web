<?php

use Bigbek\Forms\ProductForm as ProductForm;

class AdminController extends Zend_Controller_Action
{

	public function productAction()
	{
		$this->view->pageTitle = "Shopn & add product";
		$this->view->bodyCopy = "<p >Fill product data.</p>";
		$form = new ProductForm;

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();

			if ($form->isValid($formData)) {
				
				
				$form->image->receive();
        //upload complete!
        //...what now?
        $location = $form->image->getFileName();
		
		
		
		
		
		# Save a display filename (the original) and the actual filename, so it can be retrieved later
    $file = new Default_Model_File();
    $file->setDisplayFilename($originalFilename['basename'])
        ->setActualFilename($newFilename)
        ->setMimeType($form->file->getMimeType())
        ->setDescription($form->description->getValue());
    $file->save();
	
	
	
	
		Zend_Debug::dump($location);
		
		
				echo 'success';

				exit;
			} else {

				$form->populate($formData);
			}
		}
		$this->view->form = $form;
	}

}