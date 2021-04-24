<?php

namespace App\Controller;

use DateTime;
use App\Entity\Log;
use App\Entity\Channel;
use App\Entity\Program;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Repository\ChannelRepository;
use App\Repository\ProgramRepository;
use App\Repository\VersionRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class WebserviceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/ws/videos", name="ws_video_list")
     */
    public function getVideos(
        VideoRepository $videoRepository
    ): JsonResponse
    {
        $log = new Log();
        $log->setCreatedAt(new DateTime())
            ->setRoute('ws_video_list')
            ;

        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return new JsonResponse([
            'videos' => $videoRepository->findVideosToDownload(true),
        ]);
    }

    /**
     * @Route("/ws/video/status/{video}/{status}", name="ws_video_status")
     */
    public function setVideoStatus(
        Video $video,
        int $status
    ): Response
    {
        $video->setStatus($status);
        $this->entityManager->flush();

        return new Response(1, 200);
    }

    /**
     * @Route("/ws/program/{idRaspberry}/{title}/{program}",
     * defaults={"program": null},
     * name="ws_program"
     * )
     */
    public function setProgram(
        int $idRaspberry,
        string $title,
        ?Program $program
    ): Response
    {
        if (null === $program ) {
            $program = new Program();
        }
        $program->setTitle(urldecode($title))
            ->setIdRaspberry($idRaspberry);
        $this->entityManager->persist($program);
        $this->entityManager->flush();

        return new Response(1, 200);
    }


    /**
     * @Route("/ws/channel/{idRaspberry}/{title}/{channel}",
     * defaults={"channel": null},
     * name="ws_channel"
     * )
     */
    public function setChannel(
        int $idRaspberry,
        string $title,
        ?Channel $channel
    ): Response
    {
        if (null === $channel ) {
            $channel = new Channel();
        }
        $channel->setTitle(urldecode($title))
            ->setIdRaspberry($idRaspberry);
        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return new Response(1, 200);
    }


    /**
     * @Route("/ws/raspberry/update/{status}", name="ws_raspberry_update")
     */
    public function raspberryUpdate(
        Request $request,
        int $status
    ): Response
    {
        $log = new Log();
        $log->setRoute($request->get('_route'))
            ->setCreatedAt(new DateTime())
            ->setStatus($status);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new Response(1, 200);
    }


    /**
     * @Route("/ws/version/last", name="ws_last_version")
     */
    public function getLastVersion(
        VersionRepository $versionRepository
    ): Response
    {
        $log = new Log();
        $log->setCreatedAt(new DateTime())
            ->setRoute('ws_last_version')
            ;

        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return new JsonResponse([
            'version' => $versionRepository->findLastVersion(),
        ]);
    }


    /**
     * @Route("/ws/version/{tag}/{status}", name="ws_version_status")
     */
    public function setVersionStatus(
        Request $request,
        VersionRepository $versionRepository,
        string $tag,
        int $status
    ): Response
    {
        $version = $versionRepository->findOneByTag($tag);

        $version->setStatus($status);
        $this->entityManager->persist($version);
        $this->entityManager->flush();

        return new Response(1, 200);
    }
}