<?php

namespace App\Controller;

use App\Auth;
use App\Logger;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Service\Catalog\UserInterestService;
use App\ViewModel\Auction\Index;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuctionController extends AbstractController
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
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @var UserInterestService
     */
    private $interestService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Auth $auth
     * @param Logger $logger
     * @param MaterialRepository $materialRepository
     * @param DeviceRepository $deviceRepository
     * @param UserInterestService $interestService
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        Logger $logger,
        MaterialRepository $materialRepository,
        DeviceRepository $deviceRepository,
        UserInterestService $interestService,
        RequestStack $requestStack,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->materialRepository = $materialRepository;
        $this->deviceRepository = $deviceRepository;
        $this->interestService = $interestService;
        $this->logger = $logger;
    }

    /**
     * @Route("/auction", methods="GET|POST", name="auction")
     */
    public function index(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }
        $this->logger->logUserRequest();

        $user = $this->auth->getUser();

        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $devices = $this->interestService->filterDevicesByExclusion(
            $this->deviceRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Index($materials, $devices);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromMaterials($materials);
            $viewModel->fillFromDevices($devices);
        } else {
            $viewModel->fillFromRequest($this->request);

            $viewModel->fillMaterials($materials, $user);
            $viewModel->fillDevices($devices, $user);

            $viewModel->fillFromMaterials($materials);
            $viewModel->fillFromDevices($devices);

            $errors = $this->validator->validate($materials);
            $errors->addAll($this->validator->validate($devices));
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('auction/index.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
