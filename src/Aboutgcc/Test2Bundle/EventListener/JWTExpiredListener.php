<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/2/2017
 * Time: 2:50 PM
 */

namespace Aboutgcc\Test2Bundle\EventListener;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class JWTExpiredListener Extends Controller
{

    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        /** @var JWTAuthenticationFailureResponse */
        $response = $event->getResponse();

        $response->setMessage('Your token is expired, please renew it.');
    }

}