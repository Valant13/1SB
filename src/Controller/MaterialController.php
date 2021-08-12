<?php

namespace App\Controller;

use App\Auth;
use App\Repository\Catalog\MaterialRepository;
use App\ViewModel\Material\Edit;
use App\ViewModel\Material\ListViewModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MaterialController extends AbstractController
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
     * @param Auth $auth
     * @param MaterialRepository $materialRepository
     * @param RequestStack $requestStack
     * @param ValidatorInterface $validator
     */
    public function __construct(
        Auth $auth,
        MaterialRepository $materialRepository,
        RequestStack $requestStack,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
        $this->materialRepository = $materialRepository;
    }

    /**
     * @Route("/materials", methods="GET", name="materials")
     */
    public function list(): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $materials = $this->materialRepository->findOrderedByName();

        $viewModel = new ListViewModel();
        $viewModel->fillFromMaterials($materials);

        return $this->render('material/list.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * @Route("/materials/{id}", methods="GET|POST", name="materials_edit")
     */
    public function edit(int $id): Response
    {
        if (!$this->auth->isAuthorized()) {
            return $this->auth->getRedirectToLogin();
        }

        $material = $this->materialRepository->find($id);
        if ($material === null) {
            return new Response('', 404);
        }

        $viewModel = new Edit();
        $viewModel->setId($material->getId());
        $viewModel->setName($material->getProduct()->getName());

        if ($this->request->getMethod() === 'GET') {
            $viewModel->fillFromMaterial($material);
        } else {
            $viewModel->fillFromRequest($this->request);
            $viewModel->fillMaterial($material);
            $material->getProduct()->setModificationUser($this->auth->getUser());
            $material->getProduct()->setModificationTime(new \DateTime());

            $errors = $this->validator->validate($material);
            if (count($errors) > 0) {
                $viewModel->addErrorsFromViolations($errors);
            } else {
                $this->getDoctrine()->getManager()->flush();
                $viewModel->addNotice('Saved');
            }
        }

        return $this->render('material/edit.html.twig', [
            'viewModel' => $viewModel,
        ]);
    }
}
