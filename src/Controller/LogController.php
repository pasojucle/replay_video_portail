<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            'logs' => $logRepository->findAllDesc(),
        ]);
    }
}
