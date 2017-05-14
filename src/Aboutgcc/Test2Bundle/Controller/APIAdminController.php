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
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Request;

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
            $statement = $em->getConnection()->prepare("select dp,name,contact_num,email,id,enabled from employer as a join fos_user as b where a.user_id=b.id ");
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
            $statement = $em->getConnection()->prepare("select dp,id,name,username,reg_number,c_name,email,contact_num,door_address,about_us from (select * from (select * from employer as a where a.user_id=:id) e join fos_user b where e.user_id=b.id) c natural join (select name as c_name, id as country_id from country) d");
            $statement->bindValue('id', $user);
            $statement->execute();
            $details=$statement->fetchAll();
            $size = count($details);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            $statement=$em->getConnection()->prepare("SELECT * FROM (SELECT * FROM `post` WHERE user_id=:id AND status>0) b JOIN (select id as country_id, name as country_name from country)c ON c.country_id=b.country_id JOIN (SELECT user_id,name as com_name from employer WHERE user_id=:id) a on a.user_id=b.user_id");
            $statement->bindValue('id', $user);
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
            return new JsonResponse([$details,$results]);
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

    /**
     *
     * @Put("user/change-password")
     *
     */
    public function changePasswordAction(Request $request){
        try{

            $userManager = $this->get('fos_user.user_manager');
            $password = $request->request->get('password');
            $newPassword1 = $request->request->get('newPassword1');
            $newPassword2 = $request->request->get('newPassword2');

            if($newPassword1!=$newPassword2 || $newPassword1==null) return new JsonResponse("bad data",JsonResponse::HTTP_NOT_ACCEPTABLE);
            $factory = $this->get('security.encoder_factory');

            $user = $this->getUser();

            $encoder = $factory->getEncoder($user);

            $passwordValid = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt()));
            if(!$passwordValid) return new JsonResponse(JsonResponse::HTTP_UNAUTHORIZED);
            $user->setPlainPassword($newPassword1);
            $userManager->updateUser($user);
            return new JsonResponse(JsonResponse::HTTP_OK);
        }catch (Exception $e){
            return new JsonResponse(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}