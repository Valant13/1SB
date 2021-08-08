<?php

namespace App\Controller;

use App\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MaterialsController extends AbstractController
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
     * @Route("/materials", methods="GET", name="get_materials")
     */
    public function getMaterials(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }

    /**
     * @Route("/materials/{id}", methods="PUT", name="put_materials")
     */
    public function putMaterials(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->forward($this->auth->getRedirectToLogin());
        }

        // TODO: use form
        $marketplacePrice = (int)$this->request->request->get('marketplace-price');
        $imageUrl = (string)$this->request->request->get('image-url');
        $wikiPageUrl = (string)$this->request->request->get('wiki-page-url');

        return $this->render('materials/index.html.twig', [
            'controller_name' => 'MaterialsController',
        ]);
    }
}
