<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PricesController extends AbstractController
{
    /**
     * @Route("/prices", methods="GET", name="get_prices")
     */
    public function getPrices(Request $request): Response
    {
        // TODO: check accept header for application/json value
        $accept = (string)$request->headers->get('accept');

        if ($accept === 'application/json') {
            return new JsonResponse();
        } else {
            return $this->render('prices/index.html.twig', [
                'controller_name' => 'PricesController',
            ]);
        }
    }

    /**
     * @Route("/prices/{id}", methods="PUT", name="put_prices")
     */
    public function putPrices(Request $request, int $id): Response
    {
        if (!is_numeric($request->request->get('auction-price'))) {
            return new Response(400);
        }

        $auctionPrice = (int)$request->request->get('auction-price');

        return $this->render('prices/index.html.twig', [
            'controller_name' => 'PricesController',
        ]);
    }
}
