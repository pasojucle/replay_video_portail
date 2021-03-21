<?php

namespace App\Controller;

use App\Repository\LogRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard(
        VideoRepository $videoRepository,
        LogRepository $logRepository
    ): Response
    {
        //$videosToDownload = $videoRepository->findVideosToDownload();

        //$videosWithError = $videoRepository->findVideosWithError();

        //$lastRaspbianUpdate = $logRepository->findLastByRoute('ws_raspbbery_update');


        return $this->render('dashboard/index.html.twig', [
            'videosToDownload' => $videoRepository->findVideosToDownload(),
            'videosWithError' => $videoRepository->findVideosWithError(),
            'lastRaspbianUpdate' => $logRepository->findLastByRoute('ws_raspbbery_update')
        ]);
    }
}
