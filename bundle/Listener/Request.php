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

namespace Novactive\Bundle\eZSlackBundle\Listener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use JMS\Serializer\Serializer;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class Request.
 */
class Request
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Request constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param Serializer              $jmsSerializer
     */
    public function __construct(ConfigResolverInterface $configResolver, Serializer $jmsSerializer)
    {
        $this->configResolver = $configResolver;
        $this->serializer     = $jmsSerializer;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $route = $event->getRequest()->get('_route');
        if (!\in_array(
            $route,
            ['novactive_ezslack_callback_message', 'novactive_ezslack_callback_command']
        )) {
            // don't do anything if it's not a Nova eZ Slack Route
            return;
        }

        $validToken = $this->configResolver->getParameter('slack_verification_token', 'nova_ezslack');
        if ('novactive_ezslack_callback_command' === $route) {
            // token is in POST
            $token = $event->getRequest()->request->get('token');
            if ($validToken === $token) {
                // we are good, return
                return;
            }
        }

        if ('novactive_ezslack_callback_message' === $route) {
            $payload = $event->getRequest()->get('payload');
            /** @var InteractiveMessage $interactiveMessage */
            $interactiveMessage = $this->serializer->deserialize($payload, InteractiveMessage::class, 'json');
            if ($interactiveMessage instanceof InteractiveMessage) {
                $event->getRequest()->attributes->set('interactiveMessage', $interactiveMessage);
                if ($validToken === $interactiveMessage->getToken()) {
                    // we are good, return
                    return;
                }
            }
        }
        $event->setResponse(
            new JsonResponse(
                [
                    'response_type'    => 'ephemeral',
                    'replace_original' => false,
                    'text'             => "Sorry, that didn't work. Please try again.",
                ]
            )
        );
    }
}
