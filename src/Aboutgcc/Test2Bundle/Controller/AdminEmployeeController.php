<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 5/4/2017
 * Time: 11:59 AM
 */

namespace Aboutgcc\Test2Bundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DriverManager;


/**
 * Class AdminEmployeeController
 * @package Aboutgcc\Test2Bundle\Controller
 * @RouteResource("admin-employee", pluralize=false)
 */
class AdminEmployeeController extends FOSRestController implements ClassResourceInterface
{
    /**
     *
     * @return mixed
     */
    public function getAction()
    {
        try{

            $roles = $this->getUser()->getRoles();
            if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
                return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            }
            $em=$this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare("select * from (select dp,cv,first_name,last_name,contact_num,user_id,status from employee) a join (select email,id,enabled from fos_user) b where a.user_id=b.id");
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            return new JsonResponse($result);
        }catch (\Exception $e){
            exit(\Doctrine\Common\Util\Debug::dump($e));
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    /**
     *
     * @Get("admin-employee/details/{user}")
     *
     */
    public function getEmployeeAction($user){
        try{

            $roles = $this->getUser()->getRoles();
            if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
                return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            }
            $em=$this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare(
                    "select * from(
                    select * from (
                    select user_id,country_id,first_name,last_name,contact_num,nic_number,door_address,about_me,initiated_date,status,cv,dp from employee where user_id=:id) e 
                    join 
                    (select username,email,id from fos_user) b 
                    where e.user_id=b.id) u 
                    natural join 
                    (select name as country_name,id as country_id from country) c");
            $statement->bindValue('id', $user);
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            $statement2=$em->getConnection()->prepare("select * from (select * from(select first_name,last_name,user_id,id as employee_id,cv,dp from employee where user_id=:id) a natural join application) c natural join (select subject,id as post_id from post) p");
            $statement2->bindValue('id', $user);
            $statement2->execute();
            $result2=$statement2->fetchAll();
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            return new JsonResponse([$result,$result2]);
        }catch (\Exception $e){
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
    /**
     *
     * @Get("admin-employee/block/{user}")
     *
     */
    public function blockEmployeeAction($user){
        try{

            $roles = $this->getUser()->getRoles();
            if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
                return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            }
            $em=$this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare("UPDATE fos_user SET enabled = 0 WHERE fos_user.id = :id;");
            $statement->bindValue('id', $user);
            $statement->execute();
            return new JsonResponse(JsonResponse::HTTP_OK);
        }catch (\Exception $e){
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     *
     * @Get("admin-employee/unblock/{user}")
     *
     */
    public function unblockEmployeeAction($user){
        try{

            $roles = $this->getUser()->getRoles();
            if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
                return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            }

            $em=$this->getDoctrine()->getManager();
            $statement=$em->getConnection()->prepare("Select * from fos_user WHERE fos_user.id = :id;");
            $statement->bindValue('id', $user);
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            $statement2 = $em->getConnection()->prepare("UPDATE fos_user SET enabled = 1 WHERE fos_user.id = :id;");
            $statement2->bindValue('id', $user);
            $statement2->execute();
            return new JsonResponse(JsonResponse::HTTP_OK);
        }catch (\Exception $e){
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}