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
use FOS\RestBundle\Controller\Annotations\Post;
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
        //creates fos_user
        $errors = array();
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $email = $request->request->get("email");
        $userManager = $this->get('fos_user.user_manager');
        $userNameValid = $this->checkUsernameAction($username);
        $emailNameValid = $this->checkEmailAction($email);
        if(!$userNameValid)array_push($errors,"USERNAME_EXISTS");
        if(!$emailNameValid)array_push($errors,"EMAIL_EXISTS");
        if(!$userNameValid || !$emailNameValid) return new JsonResponse($errors,JsonResponse::HTTP_NOT_ACCEPTABLE);
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
            $employer->setDp("DEFAULT.jpeg");
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
     * @Post("edit-employer-info")
     */
    public function editDetailsAction(Request $request){
        $validation = $this->validateUser();
        if($validation!='true') return $validation;

        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $employer=$em->getRepository("AboutgccTest2Bundle:Employer")->findOneBy(array("userId"=>$user->getId()));
//        exit(\Doctrine\Common\Util\Debug::dump($employer));
        $errors = array();
        $username = $request->request->get("username");
        $email = $request->request->get("email");

        $userNameValid =$user->getUsername()==$username? true: $this->checkUsernameAction($username);
        $emailNameValid =$user->getEmail()==$email?true: $this->checkEmailAction($email);

        if(!$userNameValid)array_push($errors,"USERNAME_EXISTS");
        if(!$emailNameValid)array_push($errors,"EMAIL_EXISTS");
        if(!$userNameValid || !$emailNameValid) return new JsonResponse($errors,JsonResponse::HTTP_NOT_ACCEPTABLE);


        $user->setUsername($username);
        $user->setEmail($email);
        try{
            $userManager->updateUser($user);
        }catch (\Exception $e){
            exit(\Doctrine\Common\Util\Debug::dump($e));
        }
        //creates employer

        $body = $request->request->all();
//        exit(\Doctrine\Common\Util\Debug::dump($employer));
        $form = $this->createForm(CreateEmployer::class, $employer);

        $form->handleRequest($request);

        try{
//            tries to persist the employer associated with the user object
            $country = $em->getRepository("AboutgccTest2Bundle:Country")->findOneBy(array("id"=>$request->request->get("country")));
            $form->submit($body,false);
            $employer->setUserId($user);
            if($country!=null){
                $employer->setCountry($country);
            }

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
    /**
     * @Get("employer/check_username/{username}")
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
                return true;
            }
            else{
                return false;
            }
        }catch(\Exception $e){
            return new JsonResponse("server Error",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @Post("set-dp-employer")
     */
    public function setDpAction(Request $request){
        try{
            $validation = $this->validateUser();
            if($validation!='true') return $validation;
            $user = $this->getUser();
            $id = $user->getId();
            $em=$this->getDoctrine()->getEntityManager();
            $employer=$em->getRepository("AboutgccTest2Bundle:Employer")->findOneBy(array("userId"=>$id));
            $file = $request->files->all()["dp"];
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                "E:/xampp/htdocs/aboutGccAsserts/DPs",
                $fileName
            );

            $employer->setDp($fileName);
            $em->persist($employer);
            $em->flush();
            return new JsonResponse($fileName,JsonResponse::HTTP_OK);
        }catch (\Exception $e){

            exit(\Doctrine\Common\Util\Debug::dump($e));
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
                return true;
            }

            else{
                return false;
            }
        }catch(\Exception $e){
            return new JsonResponse("server Error",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Get("full_info_employer/{id}")
     */
    public function getFullDetailsAction($id){
        $user=$this->getUser();
        if($user->getId()!=$id){
            return new JsonResponse("unautherized",JsonResponse::HTTP_UNAUTHORIZED);
        }
        try{
            $em=$this->getDoctrine()->getManager();
            $statement = $em->getConnection()->prepare("select dp,id,name,username,reg_number,c_name,country_id,email,contact_num,door_address,about_us from (select * from (select * from employer as a where a.user_id=:id) e join fos_user b where e.user_id=b.id) c natural join (select name as c_name, id as country_id from country) d");
            $statement->bindValue('id', $id);
            $statement->execute();
            $result=$statement->fetchAll();
            $size = count($result);
            if($size===0){
                return new JsonResponse('no content found', JsonResponse::HTTP_NO_CONTENT);

            }
            return new JsonResponse($result);
        }catch(\Exception $e){
            return new JsonResponse("server Error",JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
}