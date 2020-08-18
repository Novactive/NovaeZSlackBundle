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
use Novactive\Bundle\eZSlackBundle\Core\Signal\Searched;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;

/**
 * Class States.
 */
class States extends AttachmentProvider
{
    /**
     * {@inheritdoc}
     */
    public function getAttachment(Signal $signal): ?Attachment
    {
        if (\count($this->actions) <= 0 || $signal instanceof Searched) {
            return null;
        }
        $attachment = new Attachment();
        $attachment->setText('_t:provider.states');
        $actions = $this->buildActions($signal);
        if (\count($actions) <= 0) {
            return null;
        }
        $attachment->setActions($actions);
        $attachment->setCallbackId($this->getAlias().'.'.time());
        $this->attachmentDecorator->decorate($attachment, 'states');

        return $attachment;
    }
}
