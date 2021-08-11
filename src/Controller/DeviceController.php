<?php

namespace App\Controller;

use App\Auth;
use App\Config;
use App\Entity\Catalog\Device;
use App\Entity\Catalog\Product;
use App\Entity\Catalog\ProductAuctionPrice;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\ResearchPointRepository;
use App\ViewModel\Device\ListModel;
use App\ViewModel\Device\Edit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeviceController extends AbstractController
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
     * @param Auth $auth
     * @param DeviceRepository $deviceRepository
     * @param MaterialRepository $materialRepository
     * @param ResearchPointRepository $researchPointRepository
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        DeviceRepository $deviceRepository,
        MaterialRepository $materialRepository,
        ResearchPointRepository $researchPointRepository,
        RequestStack $requestStack,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->materialRepository = $materialRepository;
        $this->researchPointRepository = $researchPointRepository;
    }

    /**
     * @Route("/devices", methods="GET", name="devices")
     */
    public function list(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $devices = $this->deviceRepository->findOrderedByName();

        $viewModel = new ListModel();
        $viewModel->fillFromDevices($devices);

        return $this->render('device/list.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/devices/new", methods="GET|POST", name="devices_new")
     */
    public function new(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

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

                return $this->redirectToRoute('devices_edit', ['id' => $device->getId()]);
            }
        }

        return $this->render('device/edit.html.twig', [
            'viewModel' => $viewModel,
        ]);

//        if (!$this->auth->isAuthorized()) {
//            return $this->forward($this->auth->getRedirectToLogin());
//        }
//
//        // TODO: use form
//        // TODO: validate name
//        $name = (string)$this->request->request->get('name');
//        $marketplacePrice = (int)$this->request->request->get('marketplace-price');
//        $imageUrl = (string)$this->request->request->get('image-url');
//        $wikiPageUrl = (string)$this->request->request->get('wiki-page-url');
//
//        if (!is_array($this->request->request->get('crafting-experience'))) {
//            return new Response('', 400);
//        }
//
//        if (!is_array($this->request->request->get('crafting-components'))) {
//            return new Response('', 400);
//        }
//
//        $craftingExperience = (array)$this->request->request->get('crafting-experience');
//        $craftingComponents = (array)$this->request->request->get('crafting-components');
//
//        return $this->render('devices/index.html.twig', [
//            'controller_name' => 'DevicesController',
//        ]);
    }

    /**
     * @Route("/devices/{id}", methods="GET|POST", name="devices_edit")
     */
    public function edit(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

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

//        if (!$this->auth->isAuthorized()) {
//            return $this->forward($this->auth->getRedirectToLogin());
//        }
//
//        // TODO: use form
//        $marketplacePrice = (int)$this->request->request->get('marketplace-price');
//        $imageUrl = (string)$this->request->request->get('image-url');
//        $wikiPageUrl = (string)$this->request->request->get('wiki-page-url');
//
//        if (!is_array($this->request->request->get('crafting-experience'))) {
//            return new Response('', 400);
//        }
//
//        if (!is_array($this->request->request->get('crafting-components'))) {
//            return new Response('', 400);
//        }
//
//        $craftingExperience = (array)$this->request->request->get('crafting-experience');
//        $craftingComponents = (array)$this->request->request->get('crafting-components');
//
//        return $this->render('devices/index.html.twig', [
//            'controller_name' => 'DevicesController',
//        ]);
    }

    /**
     * @Route("/devices/{id}/delete", methods="POST", name="devices_delete")
     */
    public function delete(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $device = $this->deviceRepository->find($id);
        if ($device === null) {
            return new Response('', 404);
        }

        $this->getDoctrine()->getManager()->remove($device);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('devices');
    }
}
