<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/16/2017
 * Time: 4:58 PM
 */

namespace Aboutgcc\Test2Bundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Aboutgcc\Test2Bundle\Entity\Employer;
use Aboutgcc\Test2Bundle\Form\CreateEmployer;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class APIEmployerController
 * @package Aboutgcc\Test2Bundle\Controller\
 * @RouteResource("employer", pluralize=false)
 */
class APIEmployerController extends FOSRestController implements ClassResourceInterface
{
    public function postAction(Request $request){
//        exit(\Doctrine\Common\Util\Debug::dump($request));

        //creates fos_user
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $email = $request->request->get("email");
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->setRoles(array("ROLE_EMPLOYER"));
        try{
            $userManager->updateUser($user);
        }catch (\Exception $e){
            exit(\Doctrine\Common\Util\Debug::dump($e));
        }
        //creates employer
        $employer=new Employer();
        $body = $request->request->all();
        $form = $this->createForm(CreateEmployer::class, $employer);
        $form->handleRequest($request);
        $em = $this->get('doctrine.orm.entity_manager');

        try{
//            tries to persist the employer associated with the user object
            $country = $em->getRepository("AboutgccTest2Bundle:Country")->findOneBy(array("id"=>$request->request->get("country")));
            $form->submit($body);
            $employer->setUserId($user);
            $employer->setCountry($country);
            $em = $this->getDoctrine()->getManager();
            $em->persist($employer);
            $em->flush();
            $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode(['roles'=>$user->getRoles(),'username'=>$user->getUsername(),'id' => $user->getId()]);


            // Return genereted tocken
            return new JsonResponse(['token' => $token,'id'=>$user->getId(),'roles'=>$user->getRoles()]);
        }catch(\Exception $e){
            //if there is something goes wrong this will terminate the process and undo all the queries
            $em->close();
            $userManager->deleteUser($user);
            throw $this->createAccessDeniedException();
        }finally{
            $em->close();
        }




    }

    
    public function back($request){
        exit(\Doctrine\Common\Util\Debug::dump($request));
    }
    /**
     *
     * @Get("employer/check_username/{username}")
     *
     */
    public function checkUsernameAction($username){
        try{
            $em=$this->getDoctrine()->getManager();
            $statement=$em->getConnection()->prepare("SELECT * FROM fos_user WHERE fos_user.username=:username");
            $statement->bindValue('username', $username);
            $statement->execute();
            $result=$statement->fetchAll();
            $size=count($result);

            if($size===0){
                return new JsonResponse("valid Username",JsonResponse::HTTP_ACCEPTED);
            }
            else{
                return new JsonResponse("invalid username",JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        }catch(\Exception $e){
            return new JsonResponse("server Error",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     *
     * @Get("employer/check_email/{email}")
     *
     */
    public function checkEmailAction($email){
        try{
            $em=$this->getDoctrine()->getManager();
            $statement=$em->getConnection()->prepare("SELECT * FROM fos_user WHERE fos_user.email=:email");
            $statement->bindValue('email', $email);
            $statement->execute();
            $result=$statement->fetchAll();
            $size=count($result);

            if($size===0){
                return new JsonResponse("valid email",JsonResponse::HTTP_ACCEPTED);
            }
            else{
                return new JsonResponse("invalid email",JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        }catch(\Exception $e){
            return new JsonResponse("server Error",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}