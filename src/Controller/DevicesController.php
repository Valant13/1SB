<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DevicesController extends AbstractController
{
    /**
     * @Route("/devices", methods="GET", name="get_devices")
     */
    public function getDevices(): Response
    {
        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices", methods="POST", name="post_devices")
     */
    public function postDevices(Request $request): Response
    {
        // TODO: use form
        // TODO: validate name
        $name = (string)$request->request->get('name');
        $marketplacePrice = (int)$request->request->get('marketplace-price');
        $imageUrl = (string)$request->request->get('image-url');
        $wikiPageUrl = (string)$request->request->get('wiki-page-url');

        if (!is_array($request->request->get('crafting-experience'))) {
            return new Response(400);
        }

        if (!is_array($request->request->get('crafting-components'))) {
            return new Response(400);
        }

        $craftingExperience = (array)$request->request->get('crafting-experience');
        $craftingComponents = (array)$request->request->get('crafting-components');

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices/{id}", methods="PUT", name="put_devices")
     */
    public function putDevices(Request $request, int $id): Response
    {
        // TODO: use form
        $marketplacePrice = (int)$request->request->get('marketplace-price');
        $imageUrl = (string)$request->request->get('image-url');
        $wikiPageUrl = (string)$request->request->get('wiki-page-url');

        if (!is_array($request->request->get('crafting-experience'))) {
            return new Response(400);
        }

        if (!is_array($request->request->get('crafting-components'))) {
            return new Response(400);
        }

        $craftingExperience = (array)$request->request->get('crafting-experience');
        $craftingComponents = (array)$request->request->get('crafting-components');

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * @Route("/devices/{id}", methods="DELETE", name="delete_devices")
     */
    public function deleteDevices(Request $request, int $id): Response
    {
        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }
}
