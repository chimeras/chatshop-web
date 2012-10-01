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


}
