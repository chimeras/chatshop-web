<?php
 
/**
 * @Entity
 * @Table(name="user")
 */
class Default_Model_User
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
 
    /** @Column(type="string") */
    private $first_name;
 
    public function setFirstName($string) {
        $this->first_name = $string;
        return true;
    }
    /** @Column(type="string") */
    private $last_name;
 
    public function setLastName($string) {
        $this->last_name = $string;
        return true;
    }
}
