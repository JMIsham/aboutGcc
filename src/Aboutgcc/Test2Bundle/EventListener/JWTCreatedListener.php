<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 2/26/2017
 * Time: 2:49 PM
 */

namespace Aboutgcc\Test2Bundle\EventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
class JWTCreatedListener extends Controller
{

    /**
    * @var RequestStack
    */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {

        $payload       = $event->getData();
        $payload['id'] = $event->getUser()->getId();
        $event->setData($payload);
    }

}