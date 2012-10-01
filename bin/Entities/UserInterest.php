<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UserInterest
 *
 * @ORM\Table(name="user_interest")
 * @ORM\Entity
 */
class UserInterest
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
     * @var Interest
     *
     * @ORM\ManyToOne(targetEntity="Interest")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="interest_id", referencedColumnName="id")
     * })
     */
    private $interest;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


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
     * Set interest
     *
     * @param Interest $interest
     * @return UserInterest
     */
    public function setInterest(\Interest $interest = null)
    {
        $this->interest = $interest;
    
        return $this;
    }

    /**
     * Get interest
     *
     * @return Interest 
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserInterest
     */
    public function setUser(\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
