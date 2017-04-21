<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/13/2017
 * Time: 8:58 AM
 */

namespace Aboutgcc\Test2Bundle\Controller;
use Aboutgcc\Test2Bundle\Entity\Post;
use Aboutgcc\Test2Bundle\Form\CreatePost;
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
 * @RouteResource("post-employer",pluralize=false)
 */

class EmployerPostController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @param Request $request
     * @return string|JsonResponse
     */
    public function postAction(Request $request){
        $response=$this->validateUser();
        if($response!="true"){
            return $response;
        }
        $user=$this->getUser();
        $em=$this->getDoctrine()->getEntityManager();
        if($request->request->get("eDate")==null){
            return new JsonResponse('bad Request', JsonResponse::HTTP_BAD_REQUEST);
        }
        //process the form and update the fields in post
        $post=new Post();
        $body = $request->request->all();
        $form = $this->createForm(CreatePost::class, $post);
        $form->handleRequest($request);
        $form->submit($body);
        $em->getConnection()->beginTransaction();
        try{
            //sets the country
            $country = $em->getRepository("AboutgccTest2Bundle:Country")->findOneBy(array("id"=>$request->request->get("country")));
            $post->setCountry($country);
            $array = new \Doctrine\Common\Collections\ArrayCollection();
            //creates the array with associated tags
//            foreach ($request->request->get("tags")as $tagId ){
//                $tag=$em->getRepository("AboutgccTest2Bundle:Tag")->findOneBy(array("id"=>$tagId));
//                $array->add($tag);
//            }
//            $post->setTag($array);
            //sets employer associated
            $employer = $em->getRepository("AboutgccTest2Bundle:Employer")->findOneBy(array("userId"=>$user->getId()));
            $post->setUserId($employer);
            //sets the current date
            $initiatedDate = date_create();
            //sets the expired date
            $expireDate = date_create($request->request->get("eDate"));
            $post->setInitiatedDate($initiatedDate);
            $post->setExpireDate($expireDate);
            //sets the status value as pending
            $post->setStatus(3);
            $em->persist($post);
            $em->flush();
            $em->getConnection()->commit();
        }catch (Exception $e){
            $em->getConnection()->rollBack();
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        }

            return new JsonResponse('Succeeded', JsonResponse::HTTP_OK);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function putAction(Request $request){

        $em=$this->getDoctrine()->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $id = $request->request->get("id");
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('unautherized', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUserWithPost($post->getUserId()->getUserId()->getId());
            if($response!="true"){
                return $response;
            }

            $body = $request->request->all();
            $form = $this->createForm(CreatePost::class, $post);
            $form->handleRequest($request);
            $form->submit($body,false);
            //sets the country
            if($request->request->get("country")!=null){
                $country = $em->getRepository("AboutgccTest2Bundle:Country")->findOneBy(array("id"=>$request->request->get("country")));
                $post->setCountry($country);
            }

            //creates the array with associated tags
            if($request->request->get("tags")!=null){
                $array = new \Doctrine\Common\Collections\ArrayCollection();
                foreach ($request->request->get("tags")as $tagId ){
                    $tag=$em->getRepository("AboutgccTest2Bundle:Tag")->findOneBy(array("id"=>$tagId));
                    $array->add($tag);
                }
                $post->setTag($array);
            }
            //sets the expire date
            if($request->request->get("eDate")!=null){
                $expireDate = date_create($request->request->get("eDate"));
                $post->setExpireDate($expireDate);
            }
            //sets the status value as updated
            $post->setStatus(4);
            $em->persist($post);
            $em->flush();
            $em->getConnection()->commit();
        }catch (Exception $e){
            $em->getConnection()->rollBack();
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
            return new JsonResponse('Succeeded', JsonResponse::HTTP_OK);

    }

    /**
     * @param Request $request
     * @return string|JsonResponse
     * @Put("post-employer/set-tags")
     *
     */
    public function setTagsAction(Request $request){
        $em=$this->getDoctrine()->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $id = $request->request->get("id");
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('unautherized', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUserWithPost($post->getUserId()->getUserId()->getId());
            if($response!="true"){
                return $response;
            }

            //creates the array with associated tags
            if($request->request->get("tags")!=null){
                $array = new \Doctrine\Common\Collections\ArrayCollection();
                foreach ($request->request->get("tags")as $tagId ){
                    $tag=$em->getRepository("AboutgccTest2Bundle:Tag")->findOneBy(array("id"=>$tagId));
                    $array->add($tag);
                }
                $post->setTag($array);
            }

            $post->setStatus(4);
            $em->persist($post);
            $em->flush();
            $em->getConnection()->commit();
        }catch (Exception $e){
            $em->getConnection()->rollBack();
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse('Succeeded', JsonResponse::HTTP_OK);

    }

    /**
     * @return JsonResponse
     * @Get("post-employer/get-all")
     */
    public function getAllAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $response=$this->validateUser();
        if($response!="true"){
            return $response;
        }
        $user=$this->getUser();
        try{

            $statement=$em->getConnection()->prepare("SELECT * FROM (SELECT * FROM `post` WHERE user_id=:id AND status>0) b JOIN (select id as country_id, name as country_name from country)c ON c.country_id=b.country_id JOIN (SELECT user_id,name as com_name from employer WHERE user_id=:id) a on a.user_id=b.user_id");
            $statement->bindValue('id', $user->getId());
            $statement->execute();
            $results=$statement->fetchAll();
            $size=count($results);
            //now results contains all the posts arrays separately.
            // the following loop will go through each result and get the list of tags associated with the result
            // and add the list of tags as tags in the results.
            for ($i=0;$i<$size;$i++){
                $statement=$em->getConnection()->prepare("select tag_id,name from (select * from post_tag where post_tag.post_id=:id) b JOIN tag on tag.id=b.tag_id");
                $statement->bindValue('id', $results[$i]["id"]);
                $statement->execute();
                $tags=$statement->fetchAll();
                $results[$i]["tags"]=$tags;
            }

            if($size===0){
                return new JsonResponse("No Posts",JsonResponse::HTTP_NO_CONTENT);
            }
            else{
                return new JsonResponse($results);
            }
        }catch(\Exception $e){
            return new JsonResponse($e,JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @return JsonResponse
     * @Get("post-employer/delete/{id}")
     */
    public function deleteAction($id){
        $em=$this->getDoctrine()->getEntityManager();
        $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
        if($post==null){
            return new JsonResponse('unautherized', JsonResponse::HTTP_NO_CONTENT);
        }
        $response=$this->validateUserWithPost($post->getUserId()->getUserId()->getId());
        if($response!="true"){
            return $response;
        }
        $post->setStatus(0);
        $em->persist($post);
        $em->flush();
        return new JsonResponse('Deleted', JsonResponse::HTTP_OK);
    }
    /**
     * @return JsonResponse
     * @Get("post-employer/suspend/{id}")
     */
    public function suspendAction($id){
        try{
            $em=$this->getDoctrine()->getEntityManager();
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('invalid post', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUserWithPost($post->getUserId()->getUserId()->getId());
            if($response!="true"){
                return $response;
            }
            if($post->getStatus()==1){
                $post->setStatus(2);
                $em->persist($post);
                $em->flush();
                return new JsonResponse('Deleted', JsonResponse::HTTP_OK);
            }
            return new JsonResponse('can\'t do the action', JsonResponse::HTTP_FORBIDDEN);
        }catch (Exception $e){
            return new JsonResponse('Oops ERROR!', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @return JsonResponse
     * @Get("post-employer/activate/{id}")
     */
    public function activateAction($id){
        try{
            $em=$this->getDoctrine()->getEntityManager();
            $post=$em->getRepository("AboutgccTest2Bundle:Post")->findOneBy(array("id"=>$id));
            if($post==null){
                return new JsonResponse('invalid post', JsonResponse::HTTP_NO_CONTENT);
            }
            $response=$this->validateUserWithPost($post->getUserId()->getUserId()->getId());
            if($response!="true"){
                return $response;
            }
            if($post->getStatus()==2){
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
     * @return string|JsonResponse
     */
    public function validateUser(){
        //this function checks for the valid user. for now only employer ca create a post
        $user=$this->getUser();
        $roles = $user->getRoles();
        if(array_search("ROLE_EMPLOYER",$roles)===false){
            return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
        }
        return "true";
    }

    /**
     * @param $id
     * @return string|JsonResponse
     */
    public function validateUserWithPost($id){
        $user=$this->getUser();
        $uId=$user->getId();
        $roles = $user->getRoles();
        //checks the user type
        if(array_search("ROLE_EMPLOYER",$roles)===false){
            return new JsonResponse('unautherized', JsonResponse::HTTP_UNAUTHORIZED);
        }
        //checks for the user assosiated with the post
        if($uId!=$id){
            return new JsonResponse('error user', JsonResponse::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        return "true";
    }
}