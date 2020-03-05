<?php

declare(strict_types=1);

namespace Hal\Bundle\PhpMetricsCollector\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('phpmetrics');
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $treeBuilder->root('phpmetrics');
        } else {
            $treeBuilder->getRootNode();
        }

        return $treeBuilder;
    }
}
