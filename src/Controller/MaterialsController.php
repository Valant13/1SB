<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MaterialsController extends AbstractController
{
    /**
     * @Route("/materials", methods="GET", name="get_materials")
     */
    public function getMaterials(): Response
    {
        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }

    /**
     * @Route("/materials/{id}", methods="PUT", name="put_materials")
     */
    public function putMaterials(Request $request, int $id): Response
    {
        // TODO: use form
        $marketplacePrice = (int)$request->request->get('marketplace-price');
        $imageUrl = (string)$request->request->get('image-url');
        $wikiPageUrl = (string)$request->request->get('wiki-page-url');

        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }
}
