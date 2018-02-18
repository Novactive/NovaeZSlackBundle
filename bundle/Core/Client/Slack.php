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

namespace Novactive\Bundle\eZSlackBundle\Core\Client;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\Serializer;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;

/**
 * Class Slack.
 */
class Slack
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var
     */
    private $channelsURI;

    /**
     * Slack constructor.
     *
     * @param Client                  $http
     * @param Serializer              $serializer
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(Client $http, Serializer $serializer, ConfigResolverInterface $configResolver)
    {
        $this->http        = $http;
        $this->serializer  = $serializer;
        $this->channelsURI = $configResolver->getParameter('notifications', 'nova_ezslack')['channels'];
    }

    /**
     * @param Message $message
     */
    public function sendNotification(Message $message): void
    {
        $headers = [
            'Content-type' => 'application/json',
        ];
        $payload = $this->serializer->serialize($message, 'json');
        foreach ($this->channelsURI as $uri) {
            $request = new Request('POST', trim($uri, '/'), $headers, $payload);
            try {
                $this->http->send($request, ['timeout' => 0.25]);
            } catch (\Exception $e) {
                // it is common slack would timeout, then we don't care
            }
        }
    }
}
