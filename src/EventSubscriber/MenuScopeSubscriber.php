<?php

namespace App\EventSubscriber;

use App\Controller\AuctionController;
use App\Controller\CalculatorController;
use App\Controller\DeviceController;
use App\Controller\MaterialController;
use App\Controller\UserController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MenuScopeSubscriber implements EventSubscriberInterface
{
    const SESSION_MENU_SCOPE_KEY = 'menu_scope';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
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

        $session = $this->requestStack->getSession();

        if ($controller instanceof AuctionController) {
            $session->set(self::SESSION_MENU_SCOPE_KEY, 'auction');
        } elseif ($controller instanceof CalculatorController) {
            $session->set(self::SESSION_MENU_SCOPE_KEY, 'calculator');
        } elseif ($controller instanceof DeviceController) {
            $session->set(self::SESSION_MENU_SCOPE_KEY, 'device');
        } elseif ($controller instanceof MaterialController) {
            $session->set(self::SESSION_MENU_SCOPE_KEY, 'material');
        } elseif ($controller instanceof UserController) {
            $session->set(self::SESSION_MENU_SCOPE_KEY, 'account');
        } else {
            $session->remove(self::SESSION_MENU_SCOPE_KEY);
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
