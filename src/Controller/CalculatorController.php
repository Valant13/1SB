<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculatorController extends AbstractController
{
    /**
     * @Route("/calculator/mining", methods="GET", name="get_calculator_mining")
     */
    public function getCalculatorMining(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/inventory", methods="GET", name="get_calculator_inventory")
     */
    public function getCalculatorInventory(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/mining/calculate", methods="POST", name="post_calculator_mining_calculate")
     */
    public function postCalculatorMiningCalculate(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }

    /**
     * @Route("/calculator/inventory/calculate", methods="POST", name="post_calculator_inventory_calculate")
     */
    public function postCalculatorInventoryCalculate(): Response
    {
        return $this->render('calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }
}
