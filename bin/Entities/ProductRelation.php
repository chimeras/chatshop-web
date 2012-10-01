<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ProductRelation
 *
 * @ORM\Table(name="product_relation")
 * @ORM\Entity
 */
class ProductRelation
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
     * @var integer $relatedProductId
     *
     * @ORM\Column(name="related_product_id", type="integer", nullable=true)
     */
    private $relatedProductId;

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
     * Set relatedProductId
     *
     * @param integer $relatedProductId
     * @return ProductRelation
     */
    public function setRelatedProductId($relatedProductId)
    {
        $this->relatedProductId = $relatedProductId;
    
        return $this;
    }

    /**
     * Get relatedProductId
     *
     * @return integer 
     */
    public function getRelatedProductId()
    {
        return $this->relatedProductId;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return ProductRelation
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
