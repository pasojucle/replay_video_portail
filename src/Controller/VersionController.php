<?php

namespace App\Controller;

use App\Entity\Version;
use App\Form\VersionType;
use App\Repository\VersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class VersionController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/versions", name="version_list")
     */
    public function list(
        VersionRepository $versionRepository
    ): Response
    {
        return $this->render('version/list.html.twig', [
            'versions' => $versionRepository->findAllByTagDesc(),
        ]);
    }

    /**
     * @Route("/version/edtit/{version}",
     *  name="version_edit",
     *  requirements={"version"="\d+"},
     *  defaults={"version": null}
     * )
     */
    public function edit(
        Request $request,
        ?Version $version
    ): Response
    {
        $form = $this->createForm(VersionType::class, $version);

        $form->handleRequest($request);

        $route = null;
        if ($request->isMethod('post')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $version = $form->getData();
                $this->entityManager->persist($version);
                $this->entityManager->flush();

                $route = $this->generateUrl('version_list');
            }

            $html = $this->renderView('version/edit.modal.html.twig', [
                'version' => $version,
                'form' => $form->createView(),
            ]);
            return new JsonResponse(['html' => $html, 'redirect' => $route]);
        }

        return $this->render('version/edit.modal.html.twig', [
            'version' => $version,
            'form' => $form->createView(),
        ]);
    }
}
