<?php

namespace App\Controller;

use DateTime;
use App\Entity\Log;
use App\Entity\Video;
use App\Entity\Channel;
use App\Entity\Program;
use App\Entity\Version;
use Doctrine\DBAL\Schema\View;
use App\Repository\VideoRepository;
use App\Repository\ChannelRepository;
use App\Repository\ProgramRepository;
use App\Repository\VersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class WebserviceController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private VideoRepository $videoRepository;
    private ProgramRepository $programRepository;
    private ChannelRepository $channelRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VideoRepository $videoRepository,
        ProgramRepository $programRepository,
        ChannelRepository $channelRepository
        )
    {
        $this->entityManager = $entityManager;
        $this->videoRepository = $videoRepository;
        $this->programRepository = $programRepository;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Route("/ws/videos", name="ws_video_list")
     */
    public function getVideos(): JsonResponse
    {
        $log = new Log();
        $log->setCreatedAt(new DateTime())
            ->setRoute('ws_video_list')
            ;

        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return new JsonResponse([
            'videos' => $this->videoRepository->findVideosToDownload(true),
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
     * @Route("/ws/video/{videoIdDevice}/{title}/{broadcastAt}/{programIdDevice}/{channelIdDevice}/{status}/{video}",
     * name="ws_video",
     * defaults={"video": null},
     * )
     */
    public function setVideo(
        int $videoIdDevice,
        string $title,
        string $broadcastAt,
        int $programIdDevice,
        int $channelIdDevice,
        int $status,
        ?Video $video
    ): Response
    {
        if (null === $video && 0 < $videoIdDevice) {
            $video = $this->videoRepository->findOneByIdDevice($videoIdDevice);
        }

        if (null === $video) {
            $video = new Video();
        }

        $program = $this->programRepository->findOneByIdDevice($programIdDevice);
        $channel = $this->channelRepository->findOneByIdDevice($channelIdDevice);

        if (0 < $videoIdDevice) {
            $video->setIdDevice($videoIdDevice);
        }
        if ($program && $channel) {
            $video
                ->setTitle(urldecode($title))
                ->setBroadcastAt(DateTime::createFromFormat('Y-m-d', $broadcastAt))
                ->setProgram($program)
                ->setChannel($channel)
                ->setStatus($status)
                ;
            $this->entityManager->persist($video);
            $this->entityManager->flush();

            return new JsonResponse([
                'id_web' => $video->getId(),
            ]);
        }

    }

    /**
     * @Route("/ws/program/{idDevice}/{title}/{program}",
     * defaults={"program": null},
     * name="ws_program"
     * )
     */
    public function setProgram(
        int $idDevice,
        string $title,
        ?Program $program
    ): Response
    {
        if (null === $program && 0 < $idDevice) {
            $program = $this->programRepository->findOneByIdDevice($idDevice);
        }
        if (null === $program) {
            $program = $this->programRepository->findOneByTitle(urldecode($title));
        }
        if (null === $program ) {
            $program = new Program();
        }
        $program->setTitle(urldecode($title))
            ->setIdDevice($idDevice);
        $this->entityManager->persist($program);
        $this->entityManager->flush();

        return new JsonResponse([
            'id_web' => $program->getId(),
        ]);
    }


    /**
     * @Route("/ws/channel/{idDevice}/{title}/{channel}",
     * defaults={"channel": null},
     * name="ws_channel"
     * )
     */
    public function setChannel(
        int $idDevice,
        string $title,
        ?Channel $channel
    ): Response
    {
        if (null === $channel && 0 < $idDevice) {
            $channel = $this->channelRepository->findOneByIdDevice($idDevice);
        }
        if (null === $channel) {
            $channel = $this->channelRepository->findOneByTitle(urldecode($title));
        }
        if (null === $channel ) {
            $channel = new Channel();
        }
        $channel->setTitle(urldecode($title))
            ->setIdDevice($idDevice);
        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return new JsonResponse([
            'id_web' => $channel->getId(),
        ]);
    }


    /**
     * @Route("/ws/update/distri/{status}", name="ws_distri_update")
     */
    public function distriUpdate(
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

        if (null !== $version) {
            $version = new Version();
        }

        $version->setStatus($status);
        $this->entityManager->persist($version);
        $this->entityManager->flush();

        return new Response(1, 200);
    }
}