<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/16/2017
 * Time: 1:37 PM
 */

namespace Aboutgcc\Test2Bundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Country
 * @package Aboutgcc\Test2Bundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "country")
 */
class Country
{

    /**
     * @ORM\Column(type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type = "string" , length = 20)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}