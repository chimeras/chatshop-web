<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Retailer
 *
 * @ORM\Table(name="retailer")
 * @ORM\Entity
 */
class Retailer
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


}
