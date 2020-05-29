<?php

namespace eap1985\NewsTopBundle\Controller;

use eap1985\NewsTopBundle\Entity\NewsTop;
use eap1985\NewsTopBundle\Form\EventType;
use eap1985\NewsTopBundle\Repository\NewsTopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/newstop", name="editor.")
 */
class NewsTopEditorController extends AbstractController
{
    /** @var EventRepository */
    private $eventRepository;

    public $isSoftDeleteEnabled;

    public function __construct(NewsTopRepository $eventRepository, bool $enableSoftDelete = false)
    {
        $this->eventRepository = $eventRepository;
        $this->isSoftDeleteEnabled = $enableSoftDelete;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $events = $this->eventRepository->findAll();
        dump($events);
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
            'isSoftDeleteEnabled' => $this->isSoftDeleteEnabled,
            'pagination' => $pagination,
        ]);
    }


    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new NewsTop();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('editor.index');
        }

        return $this->render('@NewsTop/editor/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        dump($hasAccess);

        $event = $this->getEvent($id);

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('editor.index');
        }

        return $this->render('@NewsTop/editor/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $event = $this->getEvent($id);

        if ($this->isSoftDeleteEnabled) {
            $event->setArchived(true);
        } else {
            $entityManager->remove($event);
        }

        $entityManager->flush();

        return $this->redirectToRoute('editor.index');
    }

    private function getEvent(int $id): NewsTop
    {

        if (!$event = $this->eventRepository->findOneBy([
            'id' => $id
            ])) {
            throw new NotFoundHttpException();
        }

        return $event;
    }
}
