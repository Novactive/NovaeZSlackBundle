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

use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class Interactions.
 */
class InteractionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(Provider::class)) {
            return;
        }

        $providerDefinition = $container->findDefinition(Provider::class);

        // Inject the Attachment Providers to the Dispatcher
        // get a mapping table to inject action in provider in the next loop
        $interactionProviders = $container->findTaggedServiceIds('novaezslack.attachment.provider');
        $providersMap         = [];
        foreach ($interactionProviders as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias                = $attributes['alias'];
                $providersMap[$alias] = $id;
                $providerDefinition->addMethodCall(
                    'addAttachmentProvider',
                    [new Reference($id), "novaezslack.provider.{$alias}"]
                );
            }
        }

        $actionProviders = $container->findTaggedServiceIds('novaezslack.action.provider');
        foreach ($actionProviders as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias                 = $attributes['alias'];
                $attachment            = $attributes['attachment'];
                $subProviderDefinition = $container->findDefinition($providersMap[$attachment]);
                $subProviderDefinition->addMethodCall(
                    'addAction',
                    [
                        new Reference($id),
                        "novaezslack.provider.{$attachment}.{$alias}",
                    ]
                );
            }
        }
    }
}
