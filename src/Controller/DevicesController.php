<?php

namespace App\Controller;

use App\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DevicesController extends AbstractController
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
     * @Route("/devices", methods="GET", name="get_devices")
     */
    public function getDevices(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices", methods="POST", name="post_devices")
     */
    public function postDevices(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        // TODO: use form
        // TODO: validate name
        $name = (string)$this->request->request->get('name');
        $marketplacePrice = (int)$this->request->request->get('marketplace-price');
        $imageUrl = (string)$this->request->request->get('image-url');
        $wikiPageUrl = (string)$this->request->request->get('wiki-page-url');

        if (!is_array($this->request->request->get('crafting-experience'))) {
            return new Response(400);
        }

        if (!is_array($this->request->request->get('crafting-components'))) {
            return new Response(400);
        }

        $craftingExperience = (array)$this->request->request->get('crafting-experience');
        $craftingComponents = (array)$this->request->request->get('crafting-components');

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices/{id}", methods="PUT", name="put_devices")
     */
    public function putDevices(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        // TODO: use form
        $marketplacePrice = (int)$this->request->request->get('marketplace-price');
        $imageUrl = (string)$this->request->request->get('image-url');
        $wikiPageUrl = (string)$this->request->request->get('wiki-page-url');

        if (!is_array($this->request->request->get('crafting-experience'))) {
            return new Response(400);
        }

        if (!is_array($this->request->request->get('crafting-components'))) {
            return new Response(400);
        }

        $craftingExperience = (array)$this->request->request->get('crafting-experience');
        $craftingComponents = (array)$this->request->request->get('crafting-components');

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices/{id}", methods="DELETE", name="delete_devices")
     */
    public function deleteDevices(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }
}
