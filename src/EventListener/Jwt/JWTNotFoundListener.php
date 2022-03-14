<?php
namespace App\EventListener\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTNotFoundListener
{
    /**
    * @param JWTNotFoundEvent $event
    */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Your token is invalid, please login again to get a new one', 401);
        $response->setData([
            'success' => false,
            'errors' => ['JWT токен не найден']
        ]);
        $event->setResponse($response);
    }
}