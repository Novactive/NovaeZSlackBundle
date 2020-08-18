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
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Option;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Select;

/**
 * Class PublicationChainChangeState.
 */
class PublicationChainChangeState extends ActionProvider
{
    /**
     * {@inheritdoc}
     */
    public function getAction(Signal $signal, int $index): ?Action
    {
        $content = $this->getContentForSignal($signal);
        if (null === $content) {
            return null;
        }

        try {
            $objectStateService = $this->repository->getObjectStateService();
            $allGroups          = $objectStateService->loadObjectStateGroups();
            foreach ($allGroups as $group) {
                if ('publication_chain' === $group->identifier) {
                    $chainGroup = $group;
                    break;
                }
            }
            if (null === $chainGroup) {
                return null;
            }
            $states = $objectStateService->loadObjectStates($chainGroup);
            $select = new Select($this->getAlias(), '_t:action.publication_chain.change_state', '');
            foreach ($states as $state) {
                $select->addOption(
                    new Option(
                        $state->getNames()[$state->mainLanguageCode],
                        "{$content->id}:{$state->id}"
                    )
                );
            }

            return $select;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InteractiveMessage $message): Attachment
    {
        $action                  = $message->getAction();
        list($contentId, $value) = explode(':', $action->getSelectedOption()->getValue());
        $attachment              = new Attachment();
        $attachment->setTitle('_t:action.publication_chain.change_state');
        try {
            $content = $this->repository->getContentService()->loadContent((int) $contentId);
            $state   = $this->repository->getObjectStateService()->loadObjectState((int) $value);
            $this->repository->getObjectStateService()->setContentState(
                $content->contentInfo,
                $state->getObjectStateGroup(),
                $state
            );
            $attachment->setColor('good');
            $attachment->setText('_t:action.state.changed');
        } catch (\Exception $e) {
            $attachment->setColor('danger');
            $attachment->setText($e->getMessage());
        }

        return $attachment;
    }
}
