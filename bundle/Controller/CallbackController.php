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
     */
    public function messageAction(
        Request $request,
        Serializer $jmsSerializer,
        Provider $provider,
        Repository $repository
    ): JsonResponse {
        // has been decoded and checked in the RequestListener already
        //@todo: add a conf to set a Slack User, in the futur try to map Slack User with eZ, Slack connect?
        $currentUser = $repository->getPermissionResolver()->getCurrentUserReference();
        $admin       = $repository->getUserService()->loadUser(14);
        $repository->getPermissionResolver()->setCurrentUserReference($admin);
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
        $repository->getPermissionResolver()->setCurrentUserReference($currentUser);

        return new JsonResponse($newPayload, 200, [], true);
    }

    /**
     * @Route("/share/{locationId}", name="novactive_ezslack_callback_shareonslack")
     * @Method({"GET"})
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
}
