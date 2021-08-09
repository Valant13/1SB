<?php

namespace App\Controller;

use App\Auth;
use App\Config;
use App\ViewModel\User\Account;
use App\ViewModel\User\Login;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param Auth $auth
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        RequestStack $requestStack,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
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
            return new Response('', 400);
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
            return new Response('', 400);
        }

        $qty = (int)$this->request->request->get('qty');
        if ($qty < 0) {
            return new Response('', 400);
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/login", methods="GET|POST", name="user_login")
     */
    public function login(): Response
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
            $viewModel->fillFromRequest($this->request);

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
     * @Route("/user/logout", methods="GET|POST", name="user_logout")
     */
    public function logout(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $this->auth->logout();

        return $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/user/account", methods="GET|POST", name="user_account")
     */
    public function account(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $user = $this->auth->getUser();
        $viewModel = new Account();

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($user);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($user);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('user/account.html.twig', [
            'viewModel' => $viewModel,
        ]);

//        if (!is_array($this->request->request->get('interest-materials'))) {
//            return new Response(400);
//        }
//
//        if (!is_array($this->request->request->get('interest-devices'))) {
//            return new Response(400);
//        }
//
//        $interestMaterials = (array)$this->request->request->get('interest-materials');
//        $interestDevices = (array)$this->request->request->get('interest-devices');
    }
}
