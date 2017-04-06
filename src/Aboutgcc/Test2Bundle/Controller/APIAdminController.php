<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 2/25/2017
 * Time: 2:28 PM
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
 * Class APIProfileController
 * @package Aboutgcc\Test2Bundle\Controller
 *
 * @RouteResource("admin", pluralize=false)
 */

class APIAdminController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Get("admin/employers")
     *
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
            $statement = $em->getConnection()->prepare("select name,contact_num,email,id,enabled from employer as a join fos_user as b where a.user_id=b.id");
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            return new JsonResponse($result);
        }catch (\Exception $e){
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    /**
     *
     * @Get("admin/employer/{user}")
     *
     */
    public function getEmployerAction($user){
        try{

            $roles = $this->getUser()->getRoles();
            if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
                return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
            }
            $em=$this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare("select * from (select * from (select * from employer as a where a.user_id=:id) e join fos_user b where e.user_id=b.id) c natural join (select name as c_name, id as country_id from country) d");
            $statement->bindValue('id', $user);
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            return new JsonResponse($result);
        }catch (\Exception $e){
            return new JsonResponse('oops there was an error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
    /**
     *
     * @Get("admin/block_employer/{user}")
     *
     */
    public function blockEmployerAction($user){
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
     * @Get("admin/unblock_employer/{user}")
     *
     */
    public function unblockEmployerAction($user){
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