<?php

namespace App\Controller;

use App\Form\LogFilterType;
use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogController extends AbstractController
{
    /**
     * @Route("/logs/{filtered}",
     * name="log_list",
     * defaults={"filtered": 0},
     * requirements={ "filtered"="[0-1]"}
     * )
     */
    public function list(
        LogRepository $logRepository,
        SessionInterface $session,
        Request $request,
        bool $filtered
    ): Response
    {
        $filters = ($filtered) ? $filters = $session->get('videoFilters') : [];

        $form = $this->createForm(LogFilterType::class);

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }
        $session->set('videoFilters', $filters);

        return $this->render('log/list.html.twig', [
            'logs' => $logRepository->findListFilterded($filters),
            'form' => $form->createView(),
        ]);
    }
}
