<?php

namespace App\EventSubscriber;

use App\Auth;
use App\AuthorizedInterface;
use App\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthSubscriber implements EventSubscriberInterface
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Auth $auth
     * @param Logger $logger
     */
    public function __construct(
        Auth $auth,
        Logger $logger
    ) {
        $this->auth = $auth;
        $this->logger = $logger;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof AuthorizedInterface) {
            if (!$this->auth->isAuthorized()) {
                $redirectToLogin = $this->auth->getRedirectToLogin();

                $event->setController(function () use ($redirectToLogin) {
                    return $redirectToLogin;
                });

                return;
            }

            $this->logger->logUserRequest();
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
