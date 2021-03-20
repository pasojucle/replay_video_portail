<?php

namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgramController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/programs/", name="program_list")
     */
    public function list(
        ProgramRepository $programRepository
    ): Response
    {
     
        return $this->render('program/list.html.twig', [
            'programs' => $programRepository->findAll(),
        ]);
    }

    /**
     * @Route("/program/edtit/{program}",
     *  name="program_edit",
     *  requirements={"program"="\d+"},
     *  defaults={"program": null}
     * )
     */
    public function edit(
        Request $request,
        ?Program $program
    ): Response
    {
        $form = $this->createForm(ProgramType::class, $program);

        if ($request->isXmlHttpRequest()) {
            return $this->render('program/edit.modal.html.twig', [
                'program' => $program,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $program = $form->getData();
            $this->entityManager->persist($program);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('program_list'));
        }
    }

    /**
     * @Route("/program/delete/{program}",
     *  name="program_delete",
     *  requirements={"program"="\d+"}
     * )
     */
    public function delete(
        FormFactoryInterface $formFactory,
        Request $request,
        Program $program
    ): Response
    {
        $form = $formFactory->create();

        if ($request->isXmlHttpRequest()) {
            return $this->render('program/delete.modal.html.twig', [
                'program' => $program,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($program);
            $this->entityManager->flush();
        }
        
        return $this->redirect($this->generateUrl('program_list'));
    }

    /**
     * @Route("/program/select", name="program_select")
     */
    public function select(
        ProgramRepository $programRepository,
        Request $request
    ): JsonResponse
    {
        $query = $request->query->get('q');

        if ($query) {
            $programs = $programRepository->findByTitle($query);
        } else {
            $programs = $programRepository->findAll();
        }
        
        $results = [];
        if (!empty($programs)) {
            foreach($programs as $program) {
                $results[] = [
                    'id' => $program->getId(),
                    'text' => $program->getTitle()
                ];
            }
        }

        return new JsonResponse($results);
    }
}
