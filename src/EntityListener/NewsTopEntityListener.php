<?php


namespace eap1985\NewsTopBundle\EntityListener;

use eap1985\NewsTopBundle\Entity\NewsBody;
use eap1985\NewsTopBundle\Entity\NewsTop;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewsTopEntityListener
{
    private $slugger;
    private $node;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(NewsTop $conference, LifecycleEventArgs $event)
    {
        $conference->computeSlug($this->slugger);
    }

    public function postPersist(NewsTop $entity, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $entity->createNode($entity, $entityManager);
    }

    public function postUpdate(NewsTop $entity, LifecycleEventArgs $args)
    {

        if(filter_var($_ENV['SITEMAP_FILE'],FILTER_VALIDATE_BOOLEAN)) {
            $entityManager = $args->getObjectManager();

            $entity = $args->getObject();

            // if this listener only applies to certain entity types,
            // add some code to check the entity type as early as possible
            if (!$entity instanceof NewsTop) {
                return;
            }

            // ... do something with the Product entity
            // Подключение к БД.
            $conn = $entityManager->getConnection();

            $out = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            // Получение статей из БД.

            $sth = $conn->prepare("SELECT * FROM `newstop`");
            $stmp = $sth->executeQuery();
            $articles = $stmp->fetchAllAssociative();

            foreach ($articles as $row) {
                // Дата изменения статьи.
                $created_at = strtotime($row['created_at']);
                $updated_at = strtotime($row['updated_at']);
                $date = max(array($created_at, $updated_at));

                $out .= '
            <url>
                <loc>https://nestop.ru/news/' . $row['slug'] . '.html</loc>
                <lastmod>' . date('Y-m-d', $date) . '</lastmod>
                <priority>' . ((($date + 604800) > time()) ? '1' : '0.5') . '</priority>
            </url>';
            }

            $out .= '</urlset>';
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . 'sitemap.xml', 'w');
            fwrite($fp, $out);
            fclose($fp);
        }

    }



    public function preUpdate(NewsTop $conference, LifecycleEventArgs $event)
    {

        $conference->computeSlug($this->slugger);
    }
}