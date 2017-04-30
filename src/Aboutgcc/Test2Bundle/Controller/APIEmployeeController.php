<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/26/2017
 * Time: 8:25 PM
 */

namespace Aboutgcc\Test2Bundle\Controller;
use Aboutgcc\Test2Bundle\Entity\Employee;
use Aboutgcc\Test2Bundle\Form\CreateEmployee;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class APIEmployeeController
 * @package Aboutgcc\Test2Bundle\Controller
 * @RouteResource("employee", pluralize=false)
 */

class APIEmployeeController extends FOSRestController implements ClassResourceInterface
{

    public function postAction(Request $request){
        //list of errors in the form
        $errors = array();

        $username = $request->request->get("username");
        $email = $request->request->get("email");
        $nicNumber = $request->request->get("nicNumber");
        $phoneNumber = $request->request->get("contactNum");

        //status of validity of the unique fields
        $userNameValid = $this->checkUsername($username);
        $emailNameValid = $this->checkEmail($email);
        $nicValid = $this->checkNic($nicNumber);
        $phoneNumberValid=$this->checkPhoneNumber($phoneNumber);

        //adds all errors if there is any
        if(!$userNameValid)array_push($errors,"USERNAME_EXISTS");
        if(!$emailNameValid)array_push($errors,"EMAIL_EXISTS");
        if(!$nicValid)array_push($errors,"NIC_EXISTS");
        if(!$phoneNumberValid)array_push($errors,"CONTACT_NUMBER_EXISTS");

        //returns the list of errors if there are any
        if(!$userNameValid || !$emailNameValid || !$phoneNumberValid || !$nicValid) return new JsonResponse($errors,JsonResponse::HTTP_NOT_ACCEPTABLE);

        //creates the fos_user
        $password = $request->request->get("password");
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->setRoles(array("ROLE_EMPLOYEE"));

        try{
            $userManager->updateUser($user);
        }catch (\Exception $e){
            exit(\Doctrine\Common\Util\Debug::dump($e));
        }

        //creates employee
        $employee=new Employee();
        $body = $request->request->all();
        $form = $this->createForm(CreateEmployee::class, $employee);
        $form->handleRequest($request);
        $em = $this->get('doctrine.orm.entity_manager');

        try{
//            tries to persist the employer associated with the user object
            $country = $em->getRepository("AboutgccTest2Bundle:Country")->findOneBy(array("id"=>$request->request->get("country")));
            $form->submit($body);
            $employee->setUserId($user);
            $employee->setCountry($country);
            $employee->setStatus(1);
            $initiatedDate = date_create();
            $employee->setInitiatedDate($initiatedDate);
//            exit(\Doctrine\Common\Util\Debug::dump($employee));
            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();
            $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode(['roles'=>$user->getRoles(),'username'=>$user->getUsername(),'id' => $user->getId()]);
            // Return genereted tocken
            $em->close();
            return new JsonResponse(['token' => $token]);
        }catch(\Exception $e){
            //if there is something goes wrong this will terminate the process and undo all the queries
            $em->close();
            $userManager->deleteUser($user);
            throw $e;
        }finally{

        }
    }
    /**
     * @param $username
     * @return bool|JsonResponse
     */
    public function checkUsername($username){
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
     * @param $email
     * @return bool|JsonResponse
     */
    public function checkEmail($email){
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
     * @param $NIC
     * @return bool|JsonResponse
     */
    public function checkNic($NIC){
        try{
            if(strlen($NIC)!=10 || strtolower(substr($NIC, -1))!="v" || !is_numeric(substr($NIC,0,9))){
//                exit(\Doctrine\Common\Util\Debug::dump(strlen($NIC)!=10,strtolower(substr($NIC, -1))!="v",!is_numeric(substr($NIC,0,9))));
                return false;
            }
            ;
            $em=$this->getDoctrine()->getManager();
            $statement=$em->getConnection()->prepare("SELECT * FROM employee WHERE employee.nic_number=:NIC");
            $statement->bindValue('NIC', $NIC);
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
     * @param $number
     * @return bool|JsonResponse
     */
    public function checkPhoneNumber($number){
        try{

            $em=$this->getDoctrine()->getManager();
            $statement=$em->getConnection()->prepare("SELECT * FROM employee WHERE employee.contact_num=:pnumber");
            $statement->bindValue('pnumber', $number);
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
}