<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Form\VideoFilterType;
use App\Repository\VideoRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VideoController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/videos/{filtered}",
     *  name="video_list",
     *  defaults={"filtered": 0},
     *  requirements={ "filtered"="[0-1]"}
     * )
     */
    public function list(
        VideoRepository $videoRepository,
        Request $request,
        SessionInterface $session,
        bool $filtered
    ): Response
    {
        $filters = ($filtered) ? $filters = $session->get('videoFilters') : [];

        $form = $this->createForm(VideoFilterType::class);

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
     
        }
        $session->set('videoFilters', $filters);
        dump($filters);  
        $videos = $videoRepository->findListFilterded($filters);

        return $this->render('video/list.html.twig', [
            'videos' => $videos,
            'form' => $form->createView(),
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

            return $this->redirect($this->generateUrl('video_list', ['filtered' => true]));
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/video/select", name="video_select")
     */
    public function select(
        VideoRepository $videoRepository,
        Request $request
    ): JsonResponse
    {
        $query = $request->query->get('q');

        if ($query) {
            $programs = $videoRepository->findByTitle($query);
        } else {
            $programs = $videoRepository->findAll();
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
