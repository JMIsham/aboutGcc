<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/30/2017
 * Time: 10:28 AM
 */

namespace Aboutgcc\Test2Bundle\Controller;

use Aboutgcc\Test2Bundle\Entity\Employee;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

USE Aboutgcc\Test2Bundle\Form\CreateEmployee;
//use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function getAllEmployerAction(){

    }
}