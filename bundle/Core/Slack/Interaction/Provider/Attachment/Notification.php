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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment;

use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;

/**
 * Class Notification.
 */
class Notification extends AttachmentProvider
{
    /**
     * {@inheritdoc}
     */
    public function getAttachment(Signal $signal): ?Attachment
    {
        if (
            class_exists(\EzSystems\Notification\Core\SignalSlot\Signal\NotificationSignal::class) &&
            !$signal instanceof \EzSystems\Notification\Core\SignalSlot\Signal\NotificationSignal
        ) {
            return null;
        }
        $data = $signal->data;

        $attachment = new Attachment();
        $attachment->setTitle($signal->type);
        if (isset($data['receiver_id'])) {
            $this->attachmentDecorator->addAuthor($attachment, $data['receiver_id']);
            $attachment->setTitle($signal->type.' -> '.$attachment->getAuthor()->getName());
        }
        $attachment->setText($data['message']);
        $attachment->setCallbackId($this->getAlias().'.'.time());
        if (isset($data['sender_id'])) {
            $this->attachmentDecorator->addAuthor($attachment, $data['sender_id']);
        }
        $this->attachmentDecorator->decorate($attachment, 'workflow');

        return $attachment;
    }
}
