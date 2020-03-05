<?php

declare(strict_types=1);

namespace Hal\Bundle\PhpMetricsCollector;

use Hal\Bundle\PhpMetricsCollector\DependencyInjection\AppExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhpMetricsCollectorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new AppExtension());
    }

    public function getContainerExtension()
    {
        return new AppExtension();
    }
}
