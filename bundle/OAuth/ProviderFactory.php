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

namespace Novactive\Bundle\eZSlackBundle\OAuth;

use AdamPaterson\OAuth2\Client\Provider\Slack;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use KnpU\OAuth2ClientBundle\DependencyInjection\ProviderFactory as BaseProviderFactory;

/**
 * Class ProviderFactory.
 *
 * Extension that makes the configuration SiteAccess Aware
 */
class ProviderFactory extends BaseProviderFactory
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @required
     *
     * @param ConfigResolverInterface $configResolver
     *
     * @return ProviderFactory
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): self
    {
        $this->configResolver = $configResolver;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createProvider($class, array $options, $redirectUri, array $redirectParams = [])
    {
        if (Slack::class === $class) {
            if ($options['clientId']) {
                $options['clientId'] = $this->configResolver->getParameter('slack_client_id', 'nova_ezslack');
            }
            if ($options['clientSecret']) {
                $options['clientSecret'] = $this->configResolver->getParameter('slack_client_secret', 'nova_ezslack');
            }
        }

        $provider = parent::createProvider(
            $class,
            $options,
            $redirectUri,
            $redirectParams
        );

        return $provider;
    }
}
