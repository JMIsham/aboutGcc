<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/2/2017
 * Time: 2:41 PM
 */

namespace Aboutgcc\Test2Bundle\EventListener;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTDecodedListener extends Controller
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
     * @param JWTDecodedEvent $event
     *
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
//        exit("meh!!");
//        $request = $this->requestStack->getCurrentRequest();
//
//        $payload = $event->getPayload();

//        if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
//            $event->markAsInvalid();
//        }
    }


}