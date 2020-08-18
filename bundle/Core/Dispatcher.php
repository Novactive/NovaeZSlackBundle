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
     * Dispatcher constructor.
     */
    public function __construct(Slack $slackClient, MessageConverter $messageConverter)
    {
        $this->slackClient = $slackClient;
        $this->messageConverter = $messageConverter;
    }

    public function receive(Signal $signal): void
    {
        $message = $this->messageConverter->convert($signal);
        $this->slackClient->sendNotification($message);
    }
}
