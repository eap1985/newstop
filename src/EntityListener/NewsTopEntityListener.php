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
        $entityManager = $args->getObjectManager();

    }

    public function preUpdate(NewsTop $conference, LifecycleEventArgs $event)
    {

        $conference->computeSlug($this->slugger);
    }
}