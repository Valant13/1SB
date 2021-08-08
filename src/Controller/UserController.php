<?php

namespace App\Controller;

use App\Auth;
use App\Config;
use App\ViewModel\User\Login;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
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
     * @Route("/user/calculator", methods="PUT", name="put_user_calculator")
     */
    public function putUserCalculator(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        $maximizationParam = $this->request->request->get('maximization-param');

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
    public function putUserMiningMaterials(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        $isAcceptable = (bool)$this->request->request->get('is-acceptable');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/inventory/materials/{id}", methods="PUT", name="put_user_inventory_materials")
     */
    public function putUserInventoryMaterials(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        if (!is_numeric($this->request->request->get('qty'))) {
            return new Response(400);
        }

        $qty = (int)$this->request->request->get('qty');
        if ($qty < 0) {
            return new Response(400);
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/login", methods="GET|POST", name="post_user_login")
     */
    public function postUserLogin(): Response
    {
        if ($this->auth->isAuthorized()) {
            return $this->redirectToRoute('homepage');
        }

        $viewModel = new Login();

        if ($this->request->getMethod() === 'GET') {
            return $this->render('user/login.html.twig', [
                'viewModel' => $viewModel
            ]);
        } else {
            $viewModel->fillByRequest($this->request);

            $nickname = $viewModel->getNickname();

            try {
                $this->auth->login($nickname);
            } catch (\InvalidArgumentException $exception) {
                try {
                    $this->auth->register($nickname);
                } catch (\InvalidArgumentException $exception) {
                    $viewModel->addError($exception->getMessage());

                    return $this->render('user/login.html.twig', [
                        'viewModel' => $viewModel
                    ]);
                }
            }

            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/user/logout", methods="GET|POST", name="post_user_logout")
     */
    public function postUserLogout(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $this->auth->logout();

        return $this->redirectToRoute('post_user_login');
    }

    /**
     * @Route("/user/account", methods="GET", name="get_user_account")
     */
    public function getUserAccount(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/account", methods="PUT", name="put_user_account")
     */
    public function putUserAccount(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        // TODO: use form
        // TODO: validate nickname
        $nickname = (string)$this->request->request->get('nickname');

        if (!is_array($this->request->request->get('interest-materials'))) {
            return new Response(400);
        }

        if (!is_array($this->request->request->get('interest-devices'))) {
            return new Response(400);
        }

        $interestMaterials = (array)$this->request->request->get('interest-materials');
        $interestDevices = (array)$this->request->request->get('interest-devices');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
