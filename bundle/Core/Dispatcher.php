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

namespace Novactive\Bundle\eZSlackBundle\Core;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot as BaseSlot;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Message as MessageConverter;

/**
 * Class Dispatcher.
 */
class Dispatcher extends BaseSlot
{
    /**
     * @var Slack
     */
    private $slackClient;

    /**
     * @var MessageConverter
     */
    private $messageConverter;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * Dispatcher constructor.
     *
     * @param Slack            $slackClient
     * @param MessageConverter $messageConverter
     * @param Repository       $repository
     */
    public function __construct(Slack $slackClient, MessageConverter $messageConverter, Repository $repository)
    {
        $this->slackClient      = $slackClient;
        $this->messageConverter = $messageConverter;
        $this->repository       = $repository;
    }

    /**
     * @param Signal $signal
     */
    public function receive(Signal $signal): void
    {
        $currentUser = $this->repository->getPermissionResolver()->getCurrentUserReference();
        $admin       = $this->repository->getUserService()->loadUser(14);
        $this->repository->getPermissionResolver()->setCurrentUserReference($admin);
        $message = $this->messageConverter->convert($signal);
        $this->slackClient->sendNotification($message);
        $this->repository->getPermissionResolver()->setCurrentUserReference($currentUser);
    }
}
