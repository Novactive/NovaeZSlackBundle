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

namespace Novactive\Bundle\eZSlackBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class Configuration.
 */
class Configuration extends SiteAccessAware\Configuration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nova_ezslack');
        $systemNode = $this->generateScopeBaseNode($rootNode);
        $systemNode
            ->scalarNode('slack_client_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('slack_client_secret')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('slack_verification_token')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('slackconnect_usergroup_content_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('slackconnect_contenttype_identifier')->cannotBeEmpty()->end()
            ->scalarNode('asset_prefix')->end()
            ->scalarNode('favicon')->end()
            ->scalarNode('site_name')->end()
            ->arrayNode('notifications')
                ->children()
                    ->arrayNode('channels')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('field_identifiers')
                ->children()
                    ->arrayNode('description')
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('image')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('styles')
                ->children()
                    ->arrayNode('attachment')
                        ->children()
                            ->scalarNode('content')->end()
                            ->scalarNode('details')->end()
                            ->scalarNode('preview')->end()
                            ->scalarNode('actions')->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
