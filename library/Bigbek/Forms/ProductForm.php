<?php

namespace Bigbek\Forms;

class ProductForm extends \Zend_Form
{

	public function __construct($options = null)
	{

		parent::__construct($options);

		$this->setName('contact_us');

		$name = new \Zend_Form_Element_Text('name');
		$name->setRequired()->addValidator('NotEmpty', true)->setLabel('Name');

		$description = new \Zend_Form_Element_Textarea('description');
		$description->setLabel('Description')
				->setAttrib('COLS', '40')
				->setAttrib('ROWS', '4');

		$image = new \Zend_Form_Element_File('image');
		$image->setLabel('image');

		$submit = new \Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->addElements(array($name, $description, $image, $submit));
		











		$title = new \Zend_Form_Element_Select('title');

		$title->setLabel('Title')
				->setMultiOptions(array('mr' => 'Mr', 'mrs' => 'Mrs'))
				->setRequired(true)->addValidator('NotEmpty', true);



		$firstName = new \Zend_Form_Element_Text('firstName');

		$firstName->setLabel('First name')
				->setRequired(true)
				->addValidator('NotEmpty');



		$lastName = new \Zend_Form_Element_Text('lastName');

		$lastName->setLabel('Last name')
				->setRequired(true)
				->addValidator('NotEmpty');



		$email = new \Zend_Form_Element_Text('email');

		$email->setLabel('Email address')
				->addFilter('StringToLower')
				->setRequired(true)
				->addValidator('NotEmpty', true)
				->addValidator('EmailAddress');
	}

}
