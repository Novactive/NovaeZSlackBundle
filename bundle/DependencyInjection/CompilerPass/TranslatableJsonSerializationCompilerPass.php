<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\DependencyInjection\CompilerPass;

use Novactive\Bundle\eZSlackBundle\Core\TranslatableJsonSerializationVisitor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @SuppressWarnings(PHPMD)
 */
class TranslatableJsonSerializationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('jms_serializer.json_serialization_visitor')) {
            return;
        }

        $jmsJsonSerializationVisitor = $container->getDefinition('jms_serializer.json_serialization_visitor');
        $jmsJsonSerializationVisitor->setClass(TranslatableJsonSerializationVisitor::class);
        $jmsJsonSerializationVisitor->addMethodCall('setTranslator', [new Reference('translator')]);
    }
}
