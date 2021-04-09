<?php

namespace App\Controller;

use DateTime;
use App\Entity\Log;
use App\Entity\Channel;
use App\Entity\Program;
use App\Repository\VideoRepository;
use App\Repository\ChannelRepository;
use App\Repository\ProgramRepository;
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
            'programs' => $videoRepository->findVideosToDownload(true),
        ]);
    }

    /**
     * @Route("/ws/video/status", name="ws_video_status")
     */
    public function setVideoStatus(
        Request $request,
        VideoRepository $videoRepository
    ): Response
    {
        $content = $request->getContent();

        $data = json_decode($content, true);

        if (null !== $data && array_key_exists('id', $data) && array_key_exists('status', $data)) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $status = filter_var($data['status'], FILTER_VALIDATE_INT);

            if (false !== $id && false !==$status) {
                $video = $videoRepository->find($id);
                if ($video) {
                                    $video->setStatus($status);
                $this->entityManager->flush();

                return new Response('', 200);
                }
            }
        }
        
        return new Response('Format de donnée incorrect', 400);
    }

    /**
     * @Route("/ws/program", name="ws_program")
     */
    public function setProgram(
        Request $request,
        ProgramRepository $programRepository
    ): Response
    {
        $content = $request->getContent();

        $data = json_decode($content, true);

        if (null !== $data && array_key_exists('id', $data) && array_key_exists('id_raspberry', $data) && array_key_exists('title', $data)) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $idRaspberry = filter_var($data['id_raspberry'], FILTER_VALIDATE_INT);
            $title = $data['title'];

            if (false !==$idRaspberry) {
                if (false !== $id ) {
                    $program = $programRepository->find($id);
                } else {
                    $program = new Channel();
                }
                $program->setTitle($title)
                    ->setIdRaspberry($idRaspberry);
                $this->entityManager->persist($program);
                $this->entityManager->flush();

                return new Response('', 200);
            }
            
        }
        
        return new Response('Format de donnée incorrect', 400);
    }


    /**
     * @Route("/ws/channel", name="ws_channel")
     */
    public function setChannel(
        Request $request,
        ChannelRepository $channelRepository
    ): Response
    {
        $content = $request->getContent();

        $data = json_decode($content, true);

        if (null !== $data && array_key_exists('id', $data) && array_key_exists('id_raspberry', $data) && array_key_exists('title', $data)) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $idRaspberry = filter_var($data['id_raspberry'], FILTER_VALIDATE_INT);
            $title = $data['title'];

            if (false !==$idRaspberry) {
                if (false !== $id ) {
                    $channel = $channelRepository->find($id);
                } else {
                    $channel = new Program();
                }
                $channel->setTitle($title)
                    ->setIdRaspberry($idRaspberry);
                $this->entityManager->persist($channel);
                $this->entityManager->flush();

                return new Response('', 200);
            }
            
        }
        
        return new Response('Format de donnée incorrect', 400);
    }


    /**
     * @Route("/ws/raspberry/update", name="ws_raspberry_update")
     */
    public function logs(
        Request $request
    ): Response
    {
        $content = $request->getContent();

        $data = json_decode($content, true);

        if (null !== $data && array_key_exists('status', $data)) {
            $status = filter_var($data['status'], FILTER_VALIDATE_INT);

            if (false !==$status) {
                $log = new Log();
                $log->setRoute($request->get('_route'))
                    ->setCreatedAt(new DateTime())
                    ->setStatus($status);
                $this->entityManager->persist($log);
                $this->entityManager->flush();

                return new Response('', 200);
            }
            
        }
        
        return new Response('Format de donnée incorrect', 400);
    }
}