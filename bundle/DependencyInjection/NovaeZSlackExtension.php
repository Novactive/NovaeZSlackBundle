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

namespace Novactive\Bundle\eZSlackBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class NovaeZSlackExtension.
 */
class NovaeZSlackExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'nova_ezslack';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('ezadminui.yml');
        $loader->load('default_settings.yml');
        $loader->load('services.yml');
        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (\in_array('EzSystemsFormBuilderBundle', $activatedBundles, true)) {
            $loader->load('interactions_services_formbuilder.yml');
        }
        if (\in_array('NotificationBundle', $activatedBundles, true)) {
            $loader->load('interactions_services_notification.yml');
        }
        $loader->load('interactions_services.yml');
        $asseticBundles   = $container->getParameter('assetic.bundles');
        $asseticBundles[] = 'NovaeZSlackBundle';
        $container->setParameter('assetic.bundles', $asseticBundles);

        $processor = new ConfigurationProcessor($container, $this->getAlias());
        $processor->mapSetting('slack_client_id', $config);
        $processor->mapSetting('slack_client_secret', $config);
        $processor->mapSetting('slack_verification_token', $config);
        $processor->mapSetting('slackconnect_usergroup_content_id', $config);
        $processor->mapSetting('slackconnect_contenttype_identifier', $config);
        $processor->mapSetting('asset_prefix', $config);
        $processor->mapSetting('favicon', $config);
        $processor->mapSetting('site_name', $config);
        $processor->mapSetting('notifications', $config);
        $processor->mapSetting('styles', $config);
        $processor->mapConfigArray('notifications', $config);
        $processor->mapConfigArray('styles', $config);
    }
}
