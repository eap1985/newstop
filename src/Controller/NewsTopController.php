<?php

namespace eap1985\NewsTopBundle\Controller;

use eap1985\NewsTopBundle\Entity\NewsTop;
use eap1985\NewsTopBundle\Repository\NewsTopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/news", name="newstop.")
 */
class NewsTopController extends AbstractController
{
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(NewsTopRepository $eventRepository,bool $enableSoftDelete = false)
    {

        $this->enableSoftDelete = $enableSoftDelete;
        $this->eventRepository = $eventRepository;
    }


    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $events = $this->eventRepository->findAll();

        $dql   = "SELECT n FROM NewsTopBundle:NewsTop n";
        $query = $em->createQuery($dql);
        foreach( $events as &$event) {
            if($event->getSi()) {
                $si = $event->getSi();
                $si = '/img/'.$si;

                if(!is_file($_SERVER['DOCUMENT_ROOT'].$si)) {
                    $event->setSi('/img/defaultimg.png');
                } else {
                    $event->setSi($si);
                }
            }
        }

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('@NewsTop/editor/index.html.twig', [
            'events' => $events,
            'isSoftDeleteEnabled' => $this->enableSoftDelete,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{slug}.html", name="show", methods={"GET"})
     */
    public function show($slug): Response
    {

        $event = $this->getEvent($slug);

        //dd($event->getNode()->getBodyValue());
        return $this->render('@NewsTop/show.html.twig', [
            'event' => $event
        ]);
    }


    private function getEvent($slug): NewsTop
    {
        if (!$event = $this->eventRepository->findBySlug($slug)) {
            throw new NotFoundHttpException();
        }

        return $event;
    }
}
