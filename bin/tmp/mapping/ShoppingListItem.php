<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ShoppingListItem
 *
 * @ORM\Table(name="shopping_list_item")
 * @ORM\Entity
 */
class ShoppingListItem
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
     * @var boolean $reminder
     *
     * @ORM\Column(name="reminder", type="boolean", nullable=true)
     */
    private $reminder;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var ShoppingList
     *
     * @ORM\ManyToOne(targetEntity="ShoppingList")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shopping_list_id", referencedColumnName="id")
     * })
     */
    private $shoppingList;

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
