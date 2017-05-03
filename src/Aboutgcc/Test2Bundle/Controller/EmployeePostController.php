<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 5/2/2017
 * Time: 6:32 PM
 */

namespace Aboutgcc\Test2Bundle\Controller;
use Aboutgcc\Test2Bundle\Entity\JobApplication;
use Aboutgcc\Test2Bundle\Entity\Post;
use Aboutgcc\Test2Bundle\Form\CreatePost;
use Doctrine\Common\Annotations\DocLexer;
use Doctrine\DBAL\Exception\ConnectionException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EmployerPostController
 * @package Aboutgcc\Test2Bundle\Controller
 * @RouteResource("post-employee",pluralize=false)
 */
class EmployeePostController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @param $postID
     * @return JsonResponse
     * @Get("post-employee/apply/{postID}")
     */
    public function applyAction($postID)
    {
        try{
            if(!$this->validateUser())  return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            $user = $this->getUser();
            $id=$user->getId();
            $em=$this->getDoctrine()->getEntityManager();
            $employee = $em->getRepository("AboutgccTest2Bundle:Employee")->findOneBy(array("userId"=>$id));
            if($this->validateEmployeeWithPost($postID,$employee->getId())!=NULL) return new JsonResponse("already applied",JsonResponse::HTTP_ALREADY_REPORTED);
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$postID));
            $date = date_create();
            $application = new JobApplication();
            $application->setEmployeeId($employee);
            $application->setPostId($post);
            $application->setDate($date);
            $application->setStatus(1);
            $em->persist($application);
            $em->flush();
            return new JsonResponse(JsonResponse::HTTP_OK);
        }
        catch (Exception $e){
            return new JsonResponse("oops! Error :(",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $applicationID
     * @return JsonResponse
     * @Get("post-employee/cancel/{applicationID}")
     */
    public function cancelAction($applicationID){
        try{
            if(!$this->validateUser())  return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            $user = $this->getUser();
            $id=$user->getId();
            $em=$this->getDoctrine()->getEntityManager();
            $employee = $em->getRepository("AboutgccTest2Bundle:Employee")->findOneBy(array("userId"=>$id));
            $application = $this->validateEmployeeWithApplication($applicationID,$employee->getId());
            if($application==NULL) return new JsonResponse("have not applied",JsonResponse::HTTP_NOT_ACCEPTABLE);
            $statement=$em->getConnection()
                ->prepare("delete from application where id=:id");
            $statement->bindValue('id', $application->getId());
            $statement->execute();
            return new JsonResponse(JsonResponse::HTTP_OK);
        }catch (Exception $e){
            return new JsonResponse("oops! Error :(",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @return JsonResponse
     * @Get("post-employee/get-all-applications")
     */
    public function getAllAction(){
        try{
            if(!$this->validateUser())  return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            $user = $this->getUser();
            $id=$user->getId();
            $em=$this->getDoctrine()->getEntityManager();
            $employee = $em->getRepository("AboutgccTest2Bundle:Employee")->findOneBy(array("userId"=>$id));
            $statement=$em->getConnection()
                ->prepare("select id,application_id,application_status,employee_id,status,subject,country_id,date from 
                      (select id as application_id,date,post_id as id,employee_id,status as application_status from application where employee_id=:id and status>0) 
                      a natural join post");
            $statement->bindValue('id', $employee->getId());
            $statement->execute();
            $applications=$statement->fetchAll();
            return new JsonResponse($applications,JsonResponse::HTTP_OK);
        }catch (Exception $e){
            return new JsonResponse("oops! Error :(",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    /**
     * @return bool
     *
     */
    public function validateUser(){
        //this function checks for the valid user. for now only employer ca create a post
        $user=$this->getUser();
        $roles = $user->getRoles();
        if(array_search("ROLE_EMPLOYEE",$roles)===false){
            false;
        }
        return true;
    }

    public function validateEmployeeWithPost ($postID,$userID){
        $em=$this->getDoctrine()->getEntityManager();
        $application=$em->getRepository("AboutgccTest2Bundle:JobApplication")->findOneBy(array("postId"=>$postID,"employeeId"=>$userID));
        return $application;

    }
    public function validateEmployeeWithApplication ($applicationID,$userID){
        $em=$this->getDoctrine()->getEntityManager();
        $application=$em->getRepository("AboutgccTest2Bundle:JobApplication")->findOneBy(array("id"=>$applicationID,"employeeId"=>$userID));
        return $application;

    }

}