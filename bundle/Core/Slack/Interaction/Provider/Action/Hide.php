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

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Button;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;

class Hide extends ActionProvider
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * Hide constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(Signal $signal, int $index): ?Action
    {
        if (!isset($signal->contentId) || $signal->contentId <= 1) {
            return null;
        }
        $content = $this->repository->getContentService()->loadContent($signal->contentId);
        if (!$content->contentInfo->published) {
            return null;
        }
        $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
        if ($location->hidden) {
            return null;
        }
        $value  = $signal->contentId;
        $button = new Button($this->getAlias(), '_t:action.hide', (string) $value);
        $button->setStyle(Button::DANGER_STYLE);

        return $button;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InteractiveMessage $message): Attachment
    {
        $action = $message->getAction();
        $value  = (int) $action->getValue();

        $attachment = new Attachment();
        $attachment->setTitle('_t:action.hide');
        try {
            $content   = $this->repository->getContentService()->loadContent($value);
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                $this->repository->getLocationService()->hideLocation($location);
            }
            $attachment->setColor('good');
            $attachment->setText('_t:action.locations.hid');
        } catch (\Exception $e) {
            $attachment->setColor('danger');
            $attachment->setText($e->getMessage());
        }

        return $attachment;
    }
}
