<?php

namespace eap1985\NewsTopBundle\Controller;

use eap1985\NewsTopBundle\Entity\NewsTop;
use eap1985\NewsTopBundle\Repository\NewsTopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/news", name="newstop.")
 */
class NewsTopController extends AbstractController
{
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(NewsTopRepository $eventRepository,bool $enableSoftDelete = false)
    {
        $this->eventRepository = $eventRepository;
    }


    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        $events = $this->eventRepository->findAll();
        dump( $events);
        return $this->render('@NewsTop/editor/index.html.twig', [
            'events' => $events,
            'isSoftDeleteEnabled' => $this->isSoftDeleteEnabled,
        ]);
    }

    /**
     * @Route("/{id}.html", name="show", methods={"GET"})
     */
    public function show(int $id): Response
    {
        $event = $this->getEvent($id);

        return $this->render('@NewsTop/show.html.twig', [
            'event' => $event
        ]);
    }


    private function getEvent(int $id): NewsTop
    {
        if (!$event = $this->eventRepository->findNotArchived($id)) {
            throw new NotFoundHttpException();
        }

        return $event;
    }
}
