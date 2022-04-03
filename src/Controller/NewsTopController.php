<?php

namespace eap1985\NewsTopBundle\Controller;

use App\Controller\AppController;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CategoryRepository;
use App\Service\MessageGenerator;
use App\SpamChecker;
use eap1985\NewsTopBundle\Entity\NewsTop;
use eap1985\NewsTopBundle\Repository\NewsTopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


use App\Repository\CommentRepository;


use Twig\Environment;

/**
 * @Route("/news", name="newstop.")
 */
class NewsTopController extends AbstractController
{
    /** @var EventRepository */
    private $eventRepository;
    private $entityManager;
    private $enableSoftDelete;
    private $twig;
    private $bus;
    private $breadcrumbs;

    public function __construct(NewsTopRepository $eventRepository,bool $enableSoftDelete = false,Environment $twig, EntityManagerInterface $entityManager,MessageBusInterface $bus, CategoryRepository $catrep)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->enableSoftDelete = $enableSoftDelete;
        $this->eventRepository = $eventRepository;
        $this->bus = $bus;
        $this->catrep = $catrep;
    }

    public function setWo($positionHandler)
    {
        $this->breadcrumbs = $positionHandler;
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
     * @Route("reload-cities", name="reloadCities")
     * @param Request $request
     * @return Response
     */
    public function reloadCitiesAction(Request $request)
    {
        $districtid = $request->request->get('id');
        switch ( $districtid ) {
            case 0:
                $select = 0;
                break;
            case 1:
                $select = 10;
                break;
            case 2:
                $select = 20;
                break;
            default:
                $select = 20;
        }
        return $this->render("@NewsTop/cities.html.twig", array("select" => $select));
    }

    /**
     * @Route("/category/{id}", name="getcategory")
     */
    public function category(EntityManagerInterface $em, Request $request, Environment $twig, NewsTop $conference, PaginatorInterface $paginator): Response
    {
        $events = $this->eventRepository->findAll();
        $id = $request->attributes->get('id');

        $dql   = "SELECT n FROM NewsTopBundle:NewsTop n WHERE n.category =".$id;
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
     * @Route("/{slug}.html", name="show")
     */
    public function show(Request $request, Environment $twig, NewsTop $conference, CommentRepository $commentRepository, $slug,SpamChecker $spamChecker,MessageGenerator $messageGenerator): Response
    {
        // Simple example
        $this->breadcrumbs->addItem("Home", $this->get("router")->generate("newstop.index"));

        $message = $messageGenerator->getHappyMessage();
        $this->addFlash('success', $message);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setNewsTop($conference);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referer'),
                'permalink' => $request->getUri(),
            ];

            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

            return $this->redirectToRoute('newstop.show', ['slug' => $conference->getSlug()
            ]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        $event = $this->getEvent($slug);

        // Example without URL
        $this->breadcrumbs->addItem($event->getName());
        //dd($event->getNode()->getBodyValue());
        $select = $this->getCategory($event);
        return new Response($twig->render('@NewsTop/show.html.twig', [
            'comment_form' => $form->createView(),
            'event' => $event,
            'select' => $select,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));

    }

    private function getCategory($event)
    {

        $cats = $this->catrep->findAll();
        $s = '';
        foreach($cats as $cat) {
            $id = $cat->getId();
            $name = $cat->getName();
            $opsel = '';

            if($event->getCategoryId()->getId() == $id) {
                $opsel = 'selected';
            }
            $s .= '<option '.$opsel.' value="'.$id.'">'.$name.'</option>';
        }

        return $cats = '<select id="categorychange" class="form-select" aria-label="Default select example">
                    <option selected>Open this select menu</option>
                    '.$s.'
                </select>';

    }


    private function getEvent($slug): NewsTop
    {
        if (!$event = $this->eventRepository->findBySlug($slug)) {
            throw new NotFoundHttpException('Not found');
        }

        $images = $this->getImage($event->getId());

        $event->images = $images;
        return $event;
    }

    public function getImage($id) {
        $conn = $this->entityManager->getConnection();
        $sql = '
            SELECT * FROM files f
            WHERE f.node_id  = ' . $id . '
            ORDER BY f.id DESC
            ';
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

}
