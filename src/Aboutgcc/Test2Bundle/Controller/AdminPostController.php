<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/13/2017
 * Time: 11:28 PM
 */

namespace Aboutgcc\Test2Bundle\Controller;
use Aboutgcc\Test2Bundle\Entity\Post;
use Aboutgcc\Test2Bundle\Form\CreatePost;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminPostController
 * @package Aboutgcc\Test2Bundle\Controller
 * @RouteResource("post-admin",pluralize=false)
 */
class AdminPostController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @return JsonResponse
     * @Get("post-admin/activate/{id}")
     */
    public function activateAction($id){
        try{
            $em=$this->getDoctrine()->getEntityManager();
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('invalid post', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUser();
            if($response!="true"){
                return $response;
            }
            if($post->getStatus()==3){
                $post->setStatus(1);
                $em->persist($post);
                $em->flush();
                return new JsonResponse('Activated', JsonResponse::HTTP_OK);
            }
            return new JsonResponse('Can\'t do the action', JsonResponse::HTTP_FORBIDDEN);
        }catch (Exception $e){
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return JsonResponse
     * @Get("post-admin/suspend/{id}")
     */
    public function suspendAction($id){
        try{
            $em=$this->getDoctrine()->getEntityManager();
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('invalid post', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUser();
            if($response!="true"){
                return $response;
            }
            //admin cannot suspend already deleted post
            if($post->getStatus()!=0){
                $post->setStatus(3);
                $em->persist($post);
                $em->flush();
                return new JsonResponse('Activated', JsonResponse::HTTP_OK);
            }
            return new JsonResponse('Can\'t do the action', JsonResponse::HTTP_FORBIDDEN);
        }catch (Exception $e){
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return string|JsonResponse
     */
    public function validateUser(){
        //this function checks for the valid user. for now only admin can do action hear
        $user=$this->getUser();
        $roles = $user->getRoles();
        if(array_search("ROLE_SUPER_ADMIN",$roles)===false){
            return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
        }
        return "true";
    }
}