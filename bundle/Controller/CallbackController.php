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

namespace Novactive\Bundle\eZSlackBundle\Controller;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Routing\ChainRouter;
use JMS\Serializer\Serializer;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Dispatcher;
use Novactive\Bundle\eZSlackBundle\Core\Signal\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Responder\FirstResponder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommandController.
 *
 * @Route("/")
 */
class CallbackController
{
    /**
     * @Route("/command", name="novactive_ezslack_callback_command")
     * @Method({"POST"})
     *
     * @param Request        $request
     * @param FirstResponder $firstResponder
     * @param Serializer     $jmsSerializer
     *
     * @return JsonResponse
     */
    public function commandAction(
        Request $request,
        FirstResponder $firstResponder,
        Serializer $jmsSerializer
    ): JsonResponse {
        $message = $firstResponder($request->request->get('text'));

        return new JsonResponse($jmsSerializer->serialize($message, 'json'), 200, [], true);
    }

    /**
     * @Route("/message", name="novactive_ezslack_callback_message")
     * @Method({"POST"})
     *
     * @param Request    $request
     * @param Serializer $jmsSerializer
     * @param Provider   $provider
     *
     * @return JsonResponse
     */
    public function messageAction(
        Request $request,
        Serializer $jmsSerializer,
        Provider $provider
    ): JsonResponse {
        // has been decoded and checked in the RequestListener already
        /** @var InteractiveMessage $interactiveMessage */
        $interactiveMessage = $request->attributes->get('interactiveMessage');
        $attachment         = $provider->execute($interactiveMessage);
        $originalMessage    = $interactiveMessage->getOriginalMessage();
        if (null === $originalMessage) {
            // we are coming from an ephemeral (prob search)
            $originalMessage = new Message();
        } else {
            $originalMessage->removeAttachmentAtIndex((int) $interactiveMessage->getAttachmentIndex() - 1);
        }
        $originalMessage->addAttachment($attachment);
        $newPayload = $jmsSerializer->serialize($originalMessage, 'json');

        return new JsonResponse($newPayload, 200, [], true);
    }

    /**
     * @Route("/share/{locationId}", name="novactive_ezslack_callback_shareonslack")
     * @Method({"GET"})
     *
     * @param Request     $request
     * @param int         $locationId
     * @param ChainRouter $router
     * @param Dispatcher  $dispatcher
     * @param Repository  $repository
     *
     * @return JsonResponse|RedirectResponse
     */
    public function shareOnSlackAction(
        Request $request,
        int $locationId,
        ChainRouter $router,
        Dispatcher $dispatcher,
        Repository $repository
    ) {
        $location  = $repository->getLocationService()->loadLocation($locationId);
        $contentId = (int) $location->contentInfo->id;
        $slot      = new Shared(['contentId' => $contentId]);
        $dispatcher->receive($slot);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse('ok');
        }

        return new RedirectResponse($router->generate('_ezpublishLocation', ['locationId' => $locationId]));
    }

    /**
     * @Route("/kcode")
     * @Method({"GET"})
     *
     * @param Slack   $client
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function kcodeAction(Slack $client, Request $request): JsonResponse
    {
        $client->sendNotification(new Message(base64_decode(base64_decode($request->query->get('m')))));

        return new JsonResponse();
    }
}
