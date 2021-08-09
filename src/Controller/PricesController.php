<?php

namespace App\Controller;

use App\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PricesController extends AbstractController
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
     * @Route("/prices", methods="GET", name="get_prices")
     */
    public function getPrices(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        // TODO: check accept header for application/json value
        $accept = (string)$this->request->headers->get('accept');

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
    public function putPrices(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        if (!is_numeric($this->request->request->get('auction-price'))) {
            return new Response('', 400);
        }

        $auctionPrice = (int)$this->request->request->get('auction-price');

        return $this->render('prices/index.html.twig', [
            'controller_name' => 'PricesController',
        ]);
    }
}
