<?php

namespace Aboutgcc\Test2Bundle\Controller;

use Aboutgcc\Test2Bundle\Entity\Employee;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

USE Aboutgcc\Test2Bundle\Form\CreateEmployee;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
       return $this->render('AboutgccTest2Bundle:Default:index.html.twig');
    }


    public function showAction()
    {
        $employees = $this->getDoctrine()->getRepository("AboutgccTest2Bundle:Employee")->findAll();
        return $this->render('AboutgccTest2Bundle:Default:show.html.twig',
            ['post'=>$employees]
            );
    }

    public function showapiAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AboutgccTest2Bundle:Employee p');
        $employees = $query->getArrayResult();
        $response = new JsonResponse();
        $response->setData($employees);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function createAction()
    {
        $employee=new Employee();
        $form = $this->createForm(CreateEmployee::class, $employee, array(
            'action' => $this->generateUrl('aboutgcc_add'),
            'method' => 'POST'
        ));
        $form->add('submit',SubmitType::class, array("label"=>"create"));
        return $this->render('AboutgccTest2Bundle:Default:create.html.twig',array('form'=>$form->createView()));
    }

    public function addAction(Request $request)
    {
        print_r($request->request->all()['create_employee']);
        $employee=new Employee();
        $body = $request->getContent();
        $form = $this->createForm(CreateEmployee::class, $employee, array(
            'action' => $this->generateUrl('aboutgcc_add'),
            'method' => 'POST'
        ));
        $form->add('submit',SubmitType::class, array("label"=>"create"));
        $form->submit($request->request->all()['create_employee']);
        //$form->handleRequest($request);
        //$this->debug_to_console($request);
        //$form->submit($body);
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();

        }
        else{
           //print_r($form->getErrors()) ;
            return $this->createAction();
        }
           return $this->showAction();

    }
    private function debug_to_console( $data ) {

        if ( is_array( $data ) )
            $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
        else
            $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

        echo $output;
    }
    public function tokenAuthenticationAction(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $user = $this->getDoctrine()->getRepository('AboutgccTest2Bundle:User')
            ->findOneBy(['username' => $username]);

        if(!$user) {
            throw $this->createNotFoundException();
        }

        if(!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
            throw $this->createAccessDeniedException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode(['roles'=>$user->getRoles(),'id' => $user->getId()]);

        return new JsonResponse(['token' => $token,'id'=>$user->getId(),'roles'=>$user->getRoles()]);
    }

}
