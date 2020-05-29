<?php
namespace eap1985\NewsTopBundle;

use eap1985\NewsTopBundle\DependencyInjection\Compiler\ExporterRegistrationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NewsTopBundle extends Bundle
{
    /**
     * Add custom compiler pass to DI compilation process
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ExporterRegistrationPass());
    }

    /**
     * This is needed to point @NewsToprBundle resource alias to root bundle dir, instead of ./src
     * Because before Symfony 5 by convention for bundle directory structure,
     * resources used to be located in ./src/Resources folder under ./src. But now not
     * @see https://github.com/symfony/symfony/blob/master/UPGRADE-5.0.md#httpkernel
     *
     * @return string
     */
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
