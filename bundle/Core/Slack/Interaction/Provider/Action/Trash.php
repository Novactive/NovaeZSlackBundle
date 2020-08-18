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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action;

use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Button;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Confirmation;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;

/**
 * Class Trash.
 */
class Trash extends ActionProvider
{
    /**
     * {@inheritdoc}
     */
    public function getAction(Signal $signal, int $index): ?Action
    {
        $content = $this->getContentForSignal($signal);
        if (null === $content || !$content->contentInfo->published ||
            $signal instanceof Signal\TrashService\TrashSignal) {
            return null;
        }
        $button = new Button($this->getAlias(), '_t:action.trash', (string) $content->id);
        $button->setStyle(Button::DANGER_STYLE);
        $confirmation = new Confirmation('_t:action.generic.confirmation');
        $button->setConfirmation($confirmation);

        return $button;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InteractiveMessage $message): Attachment
    {
        $action     = $message->getAction();
        $value      = (int) $action->getValue();
        $attachment = new Attachment();
        $attachment->setTitle('_t:action.trash');
        try {
            $content = $this->repository->getContentService()->loadContent($value);
            if (!$content->contentInfo->published) {
                $attachment->setColor('danger');
                $attachment->setText(var_export($content->contentInfo, true));
            } else {
                $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
                foreach ($locations as $location) {
                    $this->repository->getTrashService()->trash($location);
                }
                $attachment->setColor('good');
                $attachment->setText('_t:action.locations.trashed');
            }
        } catch (\Exception $e) {
            $attachment->setColor('danger');
            $attachment->setText($e->getMessage());
        }

        return $attachment;
    }
}
