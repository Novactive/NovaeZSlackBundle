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
 * Class BasicActions.
 */
class BasicActions extends AttachmentProvider
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
        $attachment->setColor('#0000ff');
        $attachment->setText('_t:provider.basic-buttons');
        $actions = $this->buildActions($signal);
        if (\count($actions) <= 0) {
            return null;
        }
        $attachment->setActions($actions);
        $attachment->setCallbackId($this->getAlias().'.'.time());

        $this->attachmentDecorator->decorate($attachment);

        return $attachment;
    }
}
