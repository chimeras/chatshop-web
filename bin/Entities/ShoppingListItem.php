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
     * Set reminder
     *
     * @param boolean $reminder
     * @return ShoppingListItem
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;
    
        return $this;
    }

    /**
     * Get reminder
     *
     * @return boolean 
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ShoppingListItem
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set shoppingList
     *
     * @param ShoppingList $shoppingList
     * @return ShoppingListItem
     */
    public function setShoppingList(\ShoppingList $shoppingList = null)
    {
        $this->shoppingList = $shoppingList;
    
        return $this;
    }

    /**
     * Get shoppingList
     *
     * @return ShoppingList 
     */
    public function getShoppingList()
    {
        return $this->shoppingList;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return ShoppingListItem
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
