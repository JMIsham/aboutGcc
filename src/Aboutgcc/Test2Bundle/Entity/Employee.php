<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 2/10/2017
 * Time: 10:49 AM
 */

namespace Aboutgcc\Test2Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="employee")
 */
class Employee

{
    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type = "string" , length = 150)
     *@Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type = "text")
     * @Assert\NotBlank()
     */
    protected $details;




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
     * Set name
     *
     * @param string $name
     *
     * @return Employee
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return Employee
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }
}
