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

namespace Novactive\Bundle\eZSlackBundle\Core\Converter;

use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Signal\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider as InteractionProvider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message as MessageModel;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Message
{
    /**
     * @var InteractionProvider
     */
    private $provider;

    /**
     * Message constructor.
     */
    public function __construct(InteractionProvider $provider)
    {
        $this->provider = $provider;
    }

    public function convert(Signal $signal, ?MessageModel $message = null): MessageModel
    {
        if (null === $message) {
            $message = new MessageModel();
        }

        if (null === $message->getText()) {
            if ($signal instanceof Signal\ContentService\PublishVersionSignal) {
                $message->setText(
                    $signal->versionNo > 1 ? '_t:message.text.content.updated' : '_t:message.text.content.created'
                );
            }
            if ($signal instanceof Signal\LocationService\HideLocationSignal) {
                $message->setText('_t:message.text.content.hid');
            }
            if ($signal instanceof Signal\LocationService\UnhideLocationSignal) {
                $message->setText('_t:message.text.content.unhid');
            }
            if ($signal instanceof Signal\TrashService\TrashSignal) {
                $message->setText('_t:message.text.content.trashed');
            }
            if ($signal instanceof Signal\TrashService\RecoverSignal) {
                $message->setText('_t:message.text.content.recovered');
            }
            if ($signal instanceof Signal\ObjectStateService\SetContentStateSignal) {
                $message->setText('_t:message.text.content.state.updated');
            }
            if ($signal instanceof Shared) {
                $message->setText('_t:message.text.content.shared');
            }
            // eZ Platform Enterprise
            if (
                class_exists(\EzSystems\FormBuilder\Core\SignalSlot\Signal\FormSubmit::class) &&
                $signal instanceof \EzSystems\FormBuilder\Core\SignalSlot\Signal\FormSubmit
            ) {
                $message->setText('_t:message.text.formsubmit');
            }
            if (
                class_exists(\EzSystems\Notification\Core\SignalSlot\Signal\NotificationSignal::class) &&
                $signal instanceof \EzSystems\Notification\Core\SignalSlot\Signal\NotificationSignal
            ) {
                $message->setText('_t:message.text.notification');
            }
        }
        $attachments = $this->provider->getAttachments($signal);
        $message->setAttachments($attachments);

        return $message;
    }
}
