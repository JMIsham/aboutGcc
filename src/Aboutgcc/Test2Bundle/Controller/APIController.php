<?php

namespace Aboutgcc\Test2Bundle\Controller;
 use FOS\RestBundle\Controller\FOSRestController;
 use FOS\RestBundle\Routing\ClassResourceInterface;
 use FOS\RestBundle\Controller\Annotations\RouteResource;


 /**
  * Class APIController
  * @package Aboutgcc\Test2Bundle\Controller
  *
  * @RouteResource("login", pluralize = false)
  */
 class APIController extends FOSRestController implements ClassResourceInterface
 {

     public function postAction(){
         exit(\Doctrine\Common\Util\Debug::dump("test"));
//         throw new \DomainException("test Error");
     }
 }