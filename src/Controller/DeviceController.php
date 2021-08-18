<?php

namespace App\Controller;

use App\Auth;
use App\AuthorizedInterface;
use App\Config;
use App\Entity\Catalog\Device;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\ResearchPointRepository;
use App\Service\Calculator\CalculatorParamsFactory;
use App\Service\Calculator\CalculatorService;
use App\ViewModel\Device\ListViewModel;
use App\ViewModel\Device\Edit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeviceController extends AbstractController implements AuthorizedInterface
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
     * @var ResearchPointRepository
     */
    private $researchPointRepository;

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
     * @param DeviceRepository $deviceRepository
     * @param MaterialRepository $materialRepository
     * @param ResearchPointRepository $researchPointRepository
     * @param CalculatorService $calculatorService
     * @param CalculatorParamsFactory $calculatorParamsFactory
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        DeviceRepository $deviceRepository,
        MaterialRepository $materialRepository,
        ResearchPointRepository $researchPointRepository,
        CalculatorService $calculatorService,
        CalculatorParamsFactory $calculatorParamsFactory,
        RequestStack $requestStack,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->materialRepository = $materialRepository;
        $this->researchPointRepository = $researchPointRepository;
        $this->calculatorService = $calculatorService;
        $this->calculatorParamsFactory = $calculatorParamsFactory;
    }

    /**
     * @Route("/devices", methods="GET", name="devices")
     */
    public function list(): Response
    {
        $devices = $this->deviceRepository->findOrderedByName();
        $materials = $this->materialRepository->findOrderedByName();

        $calculatorParams = $this->calculatorParamsFactory->createParams($materials, $devices);
        $deviceCosts = $this->calculatorService->calculateDeviceCosts($calculatorParams);

        $viewModel = new ListViewModel();
        $viewModel->fillFromDevices($devices, $deviceCosts);

        return $this->render('device/list.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/devices/new", methods="GET|POST", name="devices_new")
     */
    public function new(): Response
    {
        if ($this->deviceRepository->count([]) >= Config::DEVICE_LIMIT) {
            return new Response('Device limit reached', 507);
        }

        $device = new Device();

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->materialRepository->findOrderedByName();
        $viewModel = new Edit($researchPoints, $materials);

        if ($this->request->getMethod() === 'POST') {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillDevice($device);
            $device->getProduct()->setModificationUser($this->auth->getUser());
            $device->getProduct()->setModificationTime(new \DateTime());

            $errors = $this->validator->validate($device);
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->persist($device);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('devices');
            }
        }

        return $this->render('device/edit.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/devices/{id}", methods="GET|POST", name="devices_edit")
     */
    public function edit(int $id): Response
    {
        $device = $this->deviceRepository->find($id);
        if ($device === null) {
            return new Response('', 404);
        }

        $researchPoints = $this->researchPointRepository->findOrderedBySortOrder();
        $materials = $this->materialRepository->findOrderedByName();
        $viewModel = new Edit($researchPoints, $materials);
        $viewModel->setId($device->getId());

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromDevice($device);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillDevice($device);
            $device->getProduct()->setModificationUser($this->auth->getUser());
            $device->getProduct()->setModificationTime(new \DateTime());

            $errors = $this->validator->validate($device);
            $errors->addAll($this->validator->validate($device->getProduct()));
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('device/edit.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/devices/{id}/delete", methods="POST", name="devices_delete")
     */
    public function delete(int $id): Response
    {
        $device = $this->deviceRepository->find($id);
        if ($device === null) {
            return new Response('', 404);
        }

        $this->getDoctrine()->getManager()->remove($device);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('devices');
    }
}
