<?php

namespace App\Controller;

use App\Auth;
use App\AuthorizedInterface;
use App\Config;
use App\Repository\Calculator\UserCalculationRepository;
use App\Repository\Calculator\UserInventoryRepository;
use App\Repository\Calculator\UserMiningRepository;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\ResearchPointRepository;
use App\Service\Calculator\CalculatorParamsFactory;
use App\Service\Calculator\CalculatorService;
use App\Service\Catalog\UserInterestService;
use App\ViewModel\Calculator\Inventory;
use App\ViewModel\Calculator\Mining;
use App\ViewModel\Calculator\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculatorController extends AbstractController implements AuthorizedInterface
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
     * @var ResearchPointRepository
     */
    private $researchPointRepository;

    /**
     * @var UserCalculationRepository
     */
    private $calculationRepository;

    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @var CalculatorService
     */
    private $calculatorService;

    /**
     * @var CalculatorParamsFactory
     */
    private $calculatorParamsFactory;

    /**
     * @param Auth $auth
     * @param RequestStack $requestStack
     * @param MaterialRepository $materialRepository
     * @param DeviceRepository $deviceRepository
     * @param ResearchPointRepository $researchPointRepository
     * @param CalculatorService $calculatorService
     * @param CalculatorParamsFactory $calculatorParamsFactory
     * @param UserMiningRepository $miningRepository
     * @param UserCalculationRepository $calculationRepository
     * @param UserInventoryRepository $inventoryRepository
     * @param UserInterestService $interestService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        RequestStack $requestStack,
        MaterialRepository $materialRepository,
        DeviceRepository $deviceRepository,
        ResearchPointRepository $researchPointRepository,
        CalculatorService $calculatorService,
        CalculatorParamsFactory $calculatorParamsFactory,
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
        $this->researchPointRepository = $researchPointRepository;
        $this->calculationRepository = $calculationRepository;
        $this->deviceRepository = $deviceRepository;
        $this->calculatorService = $calculatorService;
        $this->calculatorParamsFactory = $calculatorParamsFactory;
    }

    /**
     * @Route("/calculator/inventory", methods="GET|POST", name="calculator_inventory")
     */
    public function inventory(): Response
    {
        $user = $this->auth->getUser();
        $userInventory = $this->inventoryRepository->findOneByUser($user);
        $userCalculation = $this->calculationRepository->findOneByUser($user);

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );
        $devices = $this->interestService->filterDevicesByExclusion(
            $this->deviceRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Inventory($materials, $devices, $researchPoints);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userCalculation, $userInventory);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userCalculation, $userInventory);

            $viewModel->addErrorsFromViolations($this->validator->validate($userCalculation));
            $viewModel->addErrorsFromViolations($this->validator->validate($userInventory));
            if (!$viewModel->hasErrors()) {
                $this->getDoctrine()->getManager()->flush();

                $calculatorParams = $this->calculatorParamsFactory->createParamsForInventory(
                    $materials,
                    $devices,
                    $userInventory->getMaterialQtys()
                );

                $deals = $this->calculatorService->calculateForInventory(
                    $calculatorParams,
                    $userCalculation->getMaximizationParamCode(),
                    Config::INVENTORY_DEALS_LIMIT
                );

                $viewModel->fillFromDeals($deals);
            }
        }

        return $this->render('calculator/inventory.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/calculator/mining", methods="GET|POST", name="calculator_mining")
     */
    public function mining(): Response
    {
        $user = $this->auth->getUser();
        $userMining = $this->miningRepository->findOneByUser($user);
        $userCalculation = $this->calculationRepository->findOneByUser($user);

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );
        $devices = $this->interestService->filterDevicesByExclusion(
            $this->deviceRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Mining($materials, $devices, $researchPoints);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userCalculation, $userMining);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userCalculation, $userMining);

            $viewModel->addErrorsFromViolations($this->validator->validate($userCalculation));
            $viewModel->addErrorsFromViolations($this->validator->validate($userMining));
            if (!$viewModel->hasErrors()) {
                $this->getDoctrine()->getManager()->flush();

                $calculatorParams = $this->calculatorParamsFactory->createParamsForMining(
                    $materials,
                    $devices,
                    $userMining->getAcceptableMaterialIds()
                );

                $deals = $this->calculatorService->calculateForMining(
                    $calculatorParams,
                    $userCalculation->getMaximizationParamCode(),
                    Config::MINING_DEALS_LIMIT
                );

                $viewModel->fillFromDeals($deals);
            }
        }

        return $this->render('calculator/mining.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/calculator/trade", methods="GET|POST", name="calculator_trade")
     */
    public function trade(): Response
    {
        $user = $this->auth->getUser();
        $userCalculation = $this->calculationRepository->findOneByUser($user);

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );
        $devices = $this->interestService->filterDevicesByExclusion(
            $this->deviceRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Trade($materials, $devices, $researchPoints);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromUser($userCalculation);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillUser($userCalculation);

            $viewModel->addErrorsFromViolations($this->validator->validate($userCalculation));
            if (!$viewModel->hasErrors()) {
                $this->getDoctrine()->getManager()->flush();

                $calculatorParams = $this->calculatorParamsFactory->createParamsForTrade($materials, $devices);

                $deals = $this->calculatorService->calculateForTrade(
                    $calculatorParams,
                    $userCalculation->getMaximizationParamCode(),
                    Config::TRADE_DEALS_LIMIT
                );

                $viewModel->fillFromDeals($deals);
            }
        }

        return $this->render('calculator/trade.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
