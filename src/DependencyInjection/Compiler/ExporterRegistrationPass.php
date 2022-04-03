<?php

namespace eap1985\NewsTopBundle\DependencyInjection\Compiler;

use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExporterRegistrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {


        if (!$container->has(FilterService::class)) {
            return;
        }


        $exporterManagerDefinition = $container->findDefinition(FilterService::class);

        $taggedServices = $container->findTaggedServiceIds('eap1985.newstop.liip');


        $exporterReferences = [];
        foreach ($taggedServices as $id => $tags) {
           // dump($id);
            $exporterReferences[] = new Reference($id);
        }

        //$exporterManagerDefinition->setArguments(['$exporters' => $exporterReferences]);
    }
}