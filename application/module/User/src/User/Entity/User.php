<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
/**
 * A user.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @property string $firstName
 * @property string $lastName
 * @property string $followers
 * @property int $age
 * @property string $location
 * @property string $gender
 * @property int $id
 */
class User implements InputFilterAwareInterface
{

	protected $inputFilter;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $firstName;

}