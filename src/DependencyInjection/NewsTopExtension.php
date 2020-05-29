<?php
namespace eap1985\NewsTopBundle\DependencyInjection;

use eap1985\NewsTopBundle\Controller\NewsTopEditorController;
use Exception;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class NewsTopExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //dd($configs);

        $container->registerForAutoconfiguration(FilterService::class)
            ->addTag('eap1985.newstop.exporter');



        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);


        $container->setParameter(
            'eap1985.newstop.enable_soft_delete',
            $config['enable_soft_delete']
        );



        $definition = $container->getDefinition(NewsTopEditorController::class);
        $definition->setArguments([
            '$enableSoftDelete' => $config['enable_soft_delete'],
        ]);
    }

}
