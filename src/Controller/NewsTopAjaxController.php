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
use Liip\ImagineBundle\Service\FilterService;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use eap1985\NewsTopBundle\Twig\TwigRuDateFilter;

/**
 * @Route("/newsajax", name="newsajax.")
 */
class NewsTopAjaxController extends AbstractController
{
    /** @var EventRepository */
    private $eventRepository;
    private $fs;

    public $isSoftDeleteEnabled;

    public function __construct(NewsTopRepository $eventRepository, bool $enableSoftDelete = false,CacheManager $liipCache)
    {
        $this->eventRepository = $eventRepository;
        $this->isSoftDeleteEnabled = $enableSoftDelete;
        $this->imagineCacheManager = $liipCache;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request,TwigRuDateFilter $datefilter): Response
    {

        $events = $this->eventRepository->findAll();
        $prepage = ceil(count($events) / 20);

        $getp = $request->query->getInt('p', 1); /*page number*/

        if ($getp == 1 || $getp == 0 || $getp > $prepage) {
            $p = 0;
            $curr = 0;
        } else {
            $p = ($getp - 1) * 20;
            $curr = $getp;
        }


        //$result = $this->mMysqli->query("SELECT * FROM statii LIMIT $p, 20");
        $events = $this->eventRepository->findBy([],null,20, $p);

        $dql = "SELECT n FROM NewsTopBundle:NewsTop n";
        $query = $em->createQuery($dql);
        $items = array();
        $i = 0;
        foreach ($events as &$event) {
            ++$i;
            if ($event->getSi()) {
                $si = $event->getSi();
                $si = '/img/' . $si;

                if (!is_file($_SERVER['DOCUMENT_ROOT'] . $si)) {
                    $event->setSi('/img/defaultimg.png');
                } else {
                    $event->setSi($si);
                }
            }

            $df = $datefilter->dateFilter($event->getTime());




            $items[$i]['n'] = $event->getName();
            $items[$i]['o'] = $event->getOpisanie();

            $items[$i]['r'] = $event->getMetka();
            $items[$i]['img'] = $event->getSi();
            $items[$i]['t'] =  $df;


            $items[$i]['h'] =  $this->generateUrl('newstop.show', ['id' =>  $event->getId()]);

            /** @var FilterService */
            $imagine =  $this->imagineCacheManager;

            // 1) Simple filter, OR
            $resourcePath = $imagine->generateUrl($items[$i]['img'], 'my_thumb');

            $items[$i]['src'] = $resourcePath;
        }

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $output = array('items' => $items, 'count' => [
            'max' => $prepage
        ], 'curr' => $curr);

        $response = new Response(json_encode($output));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('charset', 'UTF-8');

        return $response;

        return $this->render('@NewsTop/editor/index.html.twig', [
            'events' => $events,
            'isSoftDeleteEnabled' => $this->isSoftDeleteEnabled,
            'pagination' => $pagination,
        ]);
    }


    // вычисляем количетсво строк
    function col()
    {
        $result = $this->mMysqli->query("SELECT * FROM statii");
        return ceil($result->num_rows / 20);
    }

    // основная функция TwitterTop - вывод данных на каждый запрос jQuery
    function query($getp)
    {
        $prepage = $this->col();

        if ($getp == 1 || $getp == 0 || $getp > $prepage) {
            $p = 0;
            $curr = 0;
        } else {
            $p = ($getp - 1) * 20;
            $curr = $getp;
        }


        $result = $this->mMysqli->query("SELECT * FROM statii LIMIT $p, 20");

        $items = '';

        if ($result) {

            while ($chat = $result->fetch_array(MYSQLI_ASSOC)) {
                $n = $this->sanitize($chat['name']);
                $o = $this->sanitize($chat['opisanie']);
                $h = $this->sanitize($chat['href']);
                $r = $this->sanitize($chat['razdel']);

                $items .= <<<EOD
			{
			"n": "{$n}",
			"h": "{$h}",
			"o": "{$o}",
			"r": "{$r}"

			},
EOD;
            }

            if ($items != '') {
                $items = substr($items, 0, -1);
            }


            header('Content-type: application/json; charset=UTF-8');

            // ответ в json, items - строки из базы
            echo '{
							"items": [
								 ' . $items . '
							],
							"count": [
						   {
								"max": "' . $prepage . '"
								
						   },
						   {
						   "curr": "' . $curr . '"
						   }
						   ]
					}';


        }

    }


    private function getEvent(int $id): NewsTop
    {
        if (!$event = $this->eventRepository->findNotArchived($id)) {
            throw new NotFoundHttpException();
        }

        return $event;
    }
}
