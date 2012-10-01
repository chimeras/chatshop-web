<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ProductLocation
 *
 * @ORM\Table(name="product_location")
 * @ORM\Entity
 */
class ProductLocation
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
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * })
     */
    private $location;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;


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
     * Set location
     *
     * @param Location $location
     * @return ProductLocation
     */
    public function setLocation(\Location $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return ProductLocation
     */
    public function setProduct(\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return Product 
     */
    public function getProduct()
    {
        return $this->product;
    }
}
