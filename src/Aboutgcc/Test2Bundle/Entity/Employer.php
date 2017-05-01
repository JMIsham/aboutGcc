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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="employer")
 */
class Employer

{
//    /**
//     * @ORM\Column(type = "integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy = "AUTO")
//     */
//    protected $id;

    /**
     *
     * @ORM\Column(type="string" , length = 150)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $userId;


    /**
     * @ORM\Column(type = "string" , length = 20)
     * @Assert\NotBlank()
     */
    protected $contactNum;

    /**
     * @ORM\Column(type = "string" , length = 100,unique=true)
     * @Assert\NotBlank()
     */
    protected $RegNumber;

    /**
     * @ORM\Column(type = "text")
     * @Assert\NotBlank()
     */
    protected $doorAddress;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity = "Country")
     * @ORM\JoinColumn(name = "country_id" , referencedColumnName = "id")
     */
    protected $country;

    /**
     * @var
     * @ORM\Column(type = "text")
     */
    protected $aboutUs;

    /**
     * @ORM\Column(type="string")
     * @Assert\File(mimeTypes={ "application/jpeg" })
     */
    protected $dp;

    /**
     * @return mixed
     */
    public function getDp()
    {
        return $this->dp;
    }

    /**
     * @param mixed $dp
     */
    public function setDp($dp)
    {
        $this->dp = $dp;
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

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getAboutUs()
    {
        return $this->aboutUs;
    }

    /**
     * @param mixed $aboutUs
     */
    public function setAboutUs($aboutUs)
    {
        $this->aboutUs = $aboutUs;
    }

    /**
     * @return mixed
     */
    public function getContactNum()
    {
        return $this->contactNum;
    }

    /**
     * @param mixed $contactNum
     */
    public function setContactNum($contactNum)
    {
        $this->contactNum = $contactNum;
    }

    /**
     * @return mixed
     */
    public function getRegNumber()
    {
        return $this->RegNumber;
    }

    /**
     * @param mixed $RegNumber
     */
    public function setRegNumber($RegNumber)
    {
        $this->RegNumber = $RegNumber;
    }

    /**
     * @return mixed
     */
    public function getDoorAddress()
    {
        return $this->doorAddress;
    }

    /**
     * @param mixed $doorAddress
     */
    public function setDoorAddress($doorAddress)
    {
        $this->doorAddress = $doorAddress;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }



}
