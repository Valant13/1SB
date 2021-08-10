<?php

namespace App\Controller;

use App\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculatorController extends AbstractController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @param RequestStack $requestStack
     * @param Auth $auth
     */
    public function __construct(
        RequestStack $requestStack,
        Auth $auth
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->auth = $auth;
    }

    /**
     * @Route("/calculator/mining", methods="GET", name="get_calculator_mining")
     */
    public function getCalculatorMining(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/inventory", methods="GET", name="get_calculator_inventory")
     */
    public function getCalculatorInventory(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/mining/calculate", methods="POST", name="post_calculator_mining_calculate")
     */
    public function postCalculatorMiningCalculate(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/inventory/calculate", methods="POST", name="post_calculator_inventory_calculate")
     */
    public function postCalculatorInventoryCalculate(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }
}
