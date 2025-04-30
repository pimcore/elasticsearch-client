<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\ElasticsearchClientBundle;

use Pimcore\Bundle\ElasticsearchClientBundle\DependencyInjection\PimcoreElasticsearchClientExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PimcoreElasticsearchClientBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new PimcoreElasticsearchClientExtension();
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
