<?php

namespace App\Controller;

use App\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/calculator", methods="PUT", name="put_user_calculator")
     */
    public function putUserCalculator(Request $request): Response
    {
        $maximizationParam = $request->request->get('maximization-param');

        if (!in_array($maximizationParam, Config::ALLOWED_MAXIMIZATION_PARAMS)) {
            return new Response(400);
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/mining/materials/{id}", methods="PUT", name="put_user_mining_materials")
     */
    public function putUserMiningMaterials(Request $request, int $id): Response
    {
        $isAcceptable = (bool)$request->request->get('is-acceptable');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/inventory/materials/{id}", methods="PUT", name="put_user_inventory_materials")
     */
    public function putUserInventoryMaterials(Request $request, int $id): Response
    {
        if (!is_numeric($request->request->get('qty'))) {
            return new Response(400);
        }

        $qty = (int)$request->request->get('qty');
        if ($qty < 0) {
            return new Response(400);
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/account/login", methods="POST", name="post_user_account_login")
     */
    public function postUserAccountLogin(Request $request): Response
    {
        $nickname = (string)$request->request->get('nickname');
        // TODO: validate nickname

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/account", methods="PUT", name="put_user_account")
     */
    public function putUserAccount(Request $request): Response
    {
        // TODO: use form
        // TODO: validate nickname
        $nickname = (string)$request->request->get('nickname');

        if (!is_array($request->request->get('interest-materials'))) {
            return new Response(400);
        }

        if (!is_array($request->request->get('interest-devices'))) {
            return new Response(400);
        }

        $interestMaterials = (array)$request->request->get('interest-materials');
        $interestDevices = (array)$request->request->get('interest-devices');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
