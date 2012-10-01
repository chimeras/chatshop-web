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


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set followers
     *
     * @param integer $followers
     * @return User
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    
        return $this;
    }

    /**
     * Get followers
     *
     * @return integer 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Set age
     *
     * @param integer $age
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;
    
        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set shoeSize
     *
     * @param string $shoeSize
     * @return User
     */
    public function setShoeSize($shoeSize)
    {
        $this->shoeSize = $shoeSize;
    
        return $this;
    }

    /**
     * Get shoeSize
     *
     * @return string 
     */
    public function getShoeSize()
    {
        return $this->shoeSize;
    }

    /**
     * Set pantsSize
     *
     * @param string $pantsSize
     * @return User
     */
    public function setPantsSize($pantsSize)
    {
        $this->pantsSize = $pantsSize;
    
        return $this;
    }

    /**
     * Get pantsSize
     *
     * @return string 
     */
    public function getPantsSize()
    {
        return $this->pantsSize;
    }

    /**
     * Set shirtSize
     *
     * @param string $shirtSize
     * @return User
     */
    public function setShirtSize($shirtSize)
    {
        $this->shirtSize = $shirtSize;
    
        return $this;
    }

    /**
     * Get shirtSize
     *
     * @return string 
     */
    public function getShirtSize()
    {
        return $this->shirtSize;
    }
}
