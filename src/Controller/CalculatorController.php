<?php

namespace App\Controller;

use App\Auth;
use App\Repository\Calculator\UserInventoryRepository;
use App\Repository\Calculator\UserMiningRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Service\Catalog\UserInterestService;
use App\ViewModel\Calculator\Inventory;
use App\ViewModel\Calculator\Mining;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculatorController extends AbstractController
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
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var UserMiningRepository
     */
    private $miningRepository;

    /**
     * @var UserInterestService
     */
    private $interestService;

    /**
     * @var UserInventoryRepository
     */
    private $inventoryRepository;

    /**
     * @param Auth $auth
     * @param RequestStack $requestStack
     * @param MaterialRepository $materialRepository
     * @param UserMiningRepository $miningRepository
     * @param UserInventoryRepository $inventoryRepository
     * @param UserInterestService $interestService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        RequestStack $requestStack,
        MaterialRepository $materialRepository,
        UserMiningRepository $miningRepository,
        UserInventoryRepository $inventoryRepository,
        UserInterestService $interestService,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->materialRepository = $materialRepository;
        $this->miningRepository = $miningRepository;
        $this->interestService = $interestService;
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * @Route("/calculator/mining", methods="GET|POST", name="calculator_mining")
     */
    public function mining(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $user = $this->auth->getUser();
        $userMining = $this->miningRepository->findOneByUser($user);

        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Mining($materials);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userMining);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userMining);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('calculator/mining.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/calculator/inventory", methods="GET|POST", name="calculator_inventory")
     */
    public function inventory(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $user = $this->auth->getUser();
        $userInventory = $this->inventoryRepository->findOneByUser($user);

        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Inventory($materials);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userInventory);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userInventory);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('calculator/inventory.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
