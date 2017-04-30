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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity
 * @ORM\Table(name="employee" )
 */
class Employee
{
    /**
     *
     * @ORM\Column(type="string" , length = 150)
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     *
     * @ORM\Column(type="string" , length = 150)
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $userId;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type = "string" , length = 20)
     * @Assert\NotBlank()
     */
    protected $contactNum;

    /**
     * @ORM\Column(type = "string" ,length=10 ,unique=true)
     * @Assert\NotBlank()
     */
    protected $nicNumber;

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
    protected $aboutMe;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity= "Tag")
     * @ORM\JoinColumn(name = "employee_tag", referencedColumnName = "id")
     */
    protected $tags;

    /**
     * @var
     * @ORM\Column(type = "date")
     * @Assert\NotBlank()
     */
    protected $initiatedDate;

    /**
     * @var
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    protected $status;

    public function __construct() {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
    public function getContactNum()
    {
        return $this->contactNum;
    }

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
     * @param mixed $contactNum
     */
    public function setContactNum($contactNum)
    {
        $this->contactNum = $contactNum;
    }

    /**
     * @return mixed
     */
    public function getNicNumber()
    {
        return $this->nicNumber;
    }

    /**
     * @param mixed $nicNumber
     */
    public function setNicNumber($nicNumber)
    {
        $this->nicNumber = $nicNumber;
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

    /**
     * @return mixed
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * @param mixed $aboutMe
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getInitiatedDate()
    {
        return $this->initiatedDate;
    }

    /**
     * @param mixed $initiatedDate
     */
    public function setInitiatedDate($initiatedDate)
    {
        $this->initiatedDate = $initiatedDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

}
