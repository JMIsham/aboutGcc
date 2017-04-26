<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/26/2017
 * Time: 8:25 PM
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
 * Class APIEmployeeController
 * @package Aboutgcc\Test2Bundle\Controller
 * @RouteResource("employee", pluralize=false)
 */

class APIEmployeeController extends FOSRestController implements ClassResourceInterface
{

}