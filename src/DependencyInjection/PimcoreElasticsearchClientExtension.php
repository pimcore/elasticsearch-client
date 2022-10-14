<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\ElasticsearchClientBundle\DependencyInjection;

use Elastic\Elasticsearch\Client;
use Pimcore\Bundle\ElasticsearchClientBundle\EsClientFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class PimcoreElasticsearchClientExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    const CLIENT_SERVICE_PREFIX = 'pimcore.elasticsearch_client.';

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $definitions = [];

        foreach ($mergedConfig['es_clients'] as $name => $clientConfig) {
            $definition = new Definition(Client::class);
            $definition->setFactory(EsClientFactory::class . '::create');
            $definition->setArgument('$logger', new Reference('logger'));
            $definition->setArgument('$configuration', $clientConfig);
            $definition->addTag('monolog.logger', ['channel' => $clientConfig['logger_channel']]);
            $definitions[self::CLIENT_SERVICE_PREFIX . $name] = $definition;
        }

        $container->addDefinitions($definitions);
    }

    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('default_config.yaml');
    }
}
