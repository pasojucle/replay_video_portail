<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/videos/{page}",
     *  name="video_list",
     *  defaults={"page": 1},
     *  requirements={"page"="\d+"}
     * )
     */
    public function list(
        VideoRepository $videoRepository,
        int $page
    ): Response
    {
        $videos = $videoRepository->findList($page);
        $videos = $videoRepository->findAll();
        dump($videos);

        return $this->render('video/list.html.twig', [
            'videos' => $videos,
        ]);
    }

        /**
     * @Route("/video/{video}",
     *  name="video_edit",
     *  requirements={"video"="\d+"},
     *  defaults={"video": null}
     * )
     */
    public function edit(
        Request $request,
        ?Video $video
    ): Response
    {
        $form = $this->createForm(VideoType::class, $video);

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $video = $form->getData();
            $this->entityManager->persist($video);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('video_list'));
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }
}
