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


}
