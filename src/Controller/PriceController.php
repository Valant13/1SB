<?php

namespace App\Controller;

use App\Auth;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Service\Catalog\UserInterestService;
use App\ViewModel\Price\Edit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController extends AbstractController
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
     * @param Auth $auth
     * @param MaterialRepository $materialRepository
     * @param DeviceRepository $deviceRepository
     * @param UserInterestService $interestService
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
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
    }

    /**
     * @Route("/prices", methods="GET|POST", name="prices")
     */
    public function edit(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $user = $this->auth->getUser();

        $materials = $this->interestService->filterMaterialsByExclusion(
            $this->materialRepository->findOrderedByName(),
            $user
        );

        $devices = $this->interestService->filterDevicesByExclusion(
            $this->deviceRepository->findOrderedByName(),
            $user
        );

        $viewModel = new Edit($materials, $devices);

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromMaterials($materials);
            $viewModel->fillFromDevices($devices);
        } else {
            // Set old prices
            $viewModel->fillFromMaterials($materials);
            $viewModel->fillFromDevices($devices);

            // Merge with new prices
            $viewModel->fillFromRequest($this->request);

            // Save only difference
            $viewModel->fillMaterials($materials, $user);
            $viewModel->fillDevices($devices, $user);

            // Set new modification
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

        return $this->render('price/edit.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
