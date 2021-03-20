<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Form\ChannelType;
use App\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class ChannelController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/channels/", name="channel_list")
     */
    public function list(
        ChannelRepository $channelRepository
    ): Response
    {
     
        return $this->render('channel/list.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/channel/edtit/{channel}",
     *  name="channel_edit",
     *  requirements={"channel"="\d+"},
     *  defaults={"channel": null}
     * )
     */
    public function edit(
        Request $request,
        ?Channel $channel
    ): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);

        if ($request->isXmlHttpRequest()) {
            return $this->render('channel/edit.modal.html.twig', [
                'channel' => $channel,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $channel = $form->getData();
            $this->entityManager->persist($channel);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('channel_list'));
        }
    }

    /**
     * @Route("/channel/delete/{channel}",
     *  name="channel_delete",
     *  requirements={"channel"="\d+"}
     * )
     */
    public function delete(
        FormFactoryInterface $formFactory,
        Request $request,
        Channel $channel
    ): Response
    {
        $form = $formFactory->create();

        if ($request->isXmlHttpRequest()) {
            return $this->render('channel/delete.modal.html.twig', [
                'channel' => $channel,
                'form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);
        if ($request->isMethod('post') && $form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($channel);
            $this->entityManager->flush();
        }
        
        return $this->redirect($this->generateUrl('channel_list'));
    }

    /**
     * @Route("/channel/select", name="channel_select")
     */
    public function select(
        ChannelRepository $channelRepository,
        Request $request
    ): JsonResponse
    {
        $query = $request->query->get('q');

        if ($query) {
            $channels = $channelRepository->findByTitle($query);
        } else {
            $channels = $channelRepository->findAll();
        }
        


        $results = [];
        if (!empty($channels)) {
            foreach($channels as $channel) {
                $results[] = [
                    'id' => $channel->getId(),
                    'text' => $channel->getTitle()
                ];
            }
        }

        return new JsonResponse($results);
    }
}
