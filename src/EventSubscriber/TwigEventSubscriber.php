<?php


namespace eap1985\NewsTopBundle\EventSubscriber;

use eap1985\NewsTopBundle\Repository\NewsTopRepository;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $newstopRepository;

    public function __construct(Environment $twig, NewsTopRepository $newstopRepository)
    {
        $this->twig = $twig;
        $this->newstopRepository = $newstopRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
  ;
        $this->twig->addGlobal('newstop', $this->newstopRepository->findLatestNews());
    }

    public static function getSubscribedEvents()
    {

        return [
            'kernel.controller' => 'onKernelController'
        ];
    }
}