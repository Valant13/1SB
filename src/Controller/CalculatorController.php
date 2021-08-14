<?php

namespace App\Controller;

use App\Auth;
use App\Logger;
use App\Repository\Calculator\UserCalculationRepository;
use App\Repository\Calculator\UserInventoryRepository;
use App\Repository\Calculator\UserMiningRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\ResearchPointRepository;
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
     * @var Logger
     */
    private $logger;

    /**
     * @var ResearchPointRepository
     */
    private $researchPointRepository;

    /**
     * @var UserCalculationRepository
     */
    private $calculationRepository;

    /**
     * @param Auth $auth
     * @param Logger $logger
     * @param RequestStack $requestStack
     * @param MaterialRepository $materialRepository
     * @param ResearchPointRepository $researchPointRepository
     * @param UserMiningRepository $miningRepository
     * @param UserCalculationRepository $calculationRepository
     * @param UserInventoryRepository $inventoryRepository
     * @param UserInterestService $interestService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        Logger $logger,
        RequestStack $requestStack,
        MaterialRepository $materialRepository,
        ResearchPointRepository $researchPointRepository,
        UserMiningRepository $miningRepository,
        UserCalculationRepository $calculationRepository,
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
        $this->logger = $logger;
        $this->researchPointRepository = $researchPointRepository;
        $this->calculationRepository = $calculationRepository;
    }

    /**
     * @Route("/calculator/mining", methods="GET|POST", name="calculator_mining")
     */
    public function mining(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }
        $this->logger->logUserRequest();

        $user = $this->auth->getUser();
        $userMining = $this->miningRepository->findOneByUser($user);
        $userCalculation = $this->calculationRepository->findOneByUser($user);

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Mining($researchPoints, $materials);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userCalculation, $userMining);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userCalculation, $userMining);

            $viewModel->addErrorsFromViolations($this->validator->validate($userCalculation));
            $viewModel->addErrorsFromViolations($this->validator->validate($userMining));
            if (!$viewModel->hasErrors()) {
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
        $this->logger->logUserRequest();

        $user = $this->auth->getUser();
        $userInventory = $this->inventoryRepository->findOneByUser($user);
        $userCalculation = $this->calculationRepository->findOneByUser($user);

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Inventory($researchPoints, $materials);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userCalculation, $userInventory);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userCalculation, $userInventory);

            $viewModel->addErrorsFromViolations($this->validator->validate($userCalculation));
            $viewModel->addErrorsFromViolations($this->validator->validate($userInventory));
            if (!$viewModel->hasErrors()) {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('calculator/inventory.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
