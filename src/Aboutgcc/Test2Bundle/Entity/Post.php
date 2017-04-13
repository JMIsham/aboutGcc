<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/10/2017
 * Time: 2:06 PM
 */

namespace Aboutgcc\Test2Bundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Post
 * @package Aboutgcc\Test2Bundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Employer")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    protected $userId;

    /**
     * @ORM\Column(type="string",length=100)
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity = "Country")
     * @ORM\JoinColumn(name = "country_id" , referencedColumnName = "id")
     */
    protected $country;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity= "Tag")
     * @ORM\JoinColumn(name = "post_tag", referencedColumnName = "id")
     */
    protected $tags;

    /**
     * @ORM\Column(type = "text")
     * @Assert\NotBlank()
     */
    protected $aboutJob;


    /**
     * @ORM\Column(type = "text")
     * @Assert\NotBlank()
     */
    protected $aboutSalary;


    /**
     * @ORM\Column(type = "text")
     * @Assert\NotBlank()
     */
    protected $aboutSkill;

    /**
     * @var
     * @ORM\Column(type = "date")
     * @Assert\NotBlank()
     */
    protected $initiatedDate;

    /**
     * @var
     * @ORM\Column(type = "date")
     * @Assert\NotBlank()
     */
    protected $expireDate;

    /**
     * @var
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    protected $status;

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
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * @param mixed $expireDate
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tags = $tag;
    }

    /**
     * @return mixed
     */
    public function getAboutJob()
    {
        return $this->aboutJob;
    }

    /**
     * @param mixed $aboutJob
     */
    public function setAboutJob($aboutJob)
    {
        $this->aboutJob = $aboutJob;
    }

    /**
     * @return mixed
     */
    public function getAboutSalary()
    {
        return $this->aboutSalary;
    }

    /**
     * @param mixed $aboutSalary
     */
    public function setAboutSalary($aboutSalary)
    {
        $this->aboutSalary = $aboutSalary;
    }

    /**
     * @return mixed
     */
    public function getAboutSkill()
    {
        return $this->aboutSkill;
    }

    /**
     * @param mixed $aboutSkill
     */
    public function setAboutSkill($aboutSkill)
    {
        $this->aboutSkill = $aboutSkill;
    }
    public function __construct() {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

}