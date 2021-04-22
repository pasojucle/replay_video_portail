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

        if ($request->isXmlHttpRequest()) {
            return $this->render('version/edit.modal.html.twig', [
                'version' => $version,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $version = $form->getData();
            $this->entityManager->persist($version);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('version_list'));
        }
    }
}
