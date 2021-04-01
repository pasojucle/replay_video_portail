<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @Route("/logs", name="log_list")
     */
    public function list(
        LogRepository $logRepository
    ): Response
    {

        return $this->render('log/list.html.twig', [
            'logs' => $logRepository->findAll(),
        ]);
    }
}
