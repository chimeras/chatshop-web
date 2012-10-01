<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var integer $followers
     *
     * @ORM\Column(name="followers", type="integer", nullable=true)
     */
    private $followers;

    /**
     * @var integer $age
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $age;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var string $gender
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    private $gender;

    /**
     * @var string $shoeSize
     *
     * @ORM\Column(name="shoe_size", type="string", length=255, nullable=true)
     */
    private $shoeSize;

    /**
     * @var string $pantsSize
     *
     * @ORM\Column(name="pants_size", type="string", length=255, nullable=true)
     */
    private $pantsSize;

    /**
     * @var string $shirtSize
     *
     * @ORM\Column(name="shirt_size", type="string", length=255, nullable=true)
     */
    private $shirtSize;


}
