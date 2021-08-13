<?php

namespace App\Controller;

use App\Auth;
use App\Config;
use App\Logger;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\UserInterestRepository;
use App\Repository\User\UserRepository;
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
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserInterestRepository
     */
    private $userInterestRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Auth $auth
     * @param RequestStack $requestStack
     * @param DeviceRepository $deviceRepository
     * @param MaterialRepository $materialRepository
     * @param UserRepository $userRepository
     * @param UserInterestRepository $userInterestRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        Logger $logger,
        RequestStack $requestStack,
        DeviceRepository $deviceRepository,
        MaterialRepository $materialRepository,
        UserRepository $userRepository,
        UserInterestRepository $userInterestRepository,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->materialRepository = $materialRepository;
        $this->userRepository = $userRepository;
        $this->userInterestRepository = $userInterestRepository;
        $this->logger = $logger;
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
                if ($this->userRepository->count([]) >= Config::USER_LIMIT) {
                    return new Response('User limit reached', 507);
                }

                try {
                    $this->auth->register($nickname);
                } catch (\InvalidArgumentException $exception) {
                    $viewModel->addError($exception->getMessage());

                    return $this->render('user/login.html.twig', [
                        'viewModel' => $viewModel
                    ]);
                }
            }

            $this->logger->logUserRequest();

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
        $this->logger->logUserRequest();

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
        $this->logger->logUserRequest();

        $user = $this->auth->getUser();
        $userInterest = $this->userInterestRepository->findOneByUser($user);

        $materials = $this->materialRepository->findOrderedByName();
        $device = $this->deviceRepository->findOrderedByName();

        $viewModel = new Account($materials, $device);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($user, $userInterest);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($user, $userInterest);

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
    }
}
