<?php

namespace App\Twig;

use App\EventSubscriber\MenuScopeSubscriber;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuScopeProvider
{
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
     * @return string|null
     */
    public function getMenuScope(): ?string
    {
        return $this->requestStack->getSession()->get(MenuScopeSubscriber::SESSION_MENU_SCOPE_KEY);
    }
}
