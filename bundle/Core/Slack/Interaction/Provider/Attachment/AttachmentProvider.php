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
use Novactive\Bundle\eZSlackBundle\Core\Decorator\Attachment as AttachmentDecorator;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\ActionProvider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\ActionProviderInterface;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\AliasTrait;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;

/**
 * Class AttachmentProvider.
 */
abstract class AttachmentProvider implements AttachmentProviderInterface
{
    use AliasTrait;

    /**
     * @var ActionProviderInterface[]
     */
    protected $actions;

    /**
     * @var AttachmentDecorator
     */
    protected $attachmentDecorator;

    /**
     * @required
     *
     * @param AttachmentDecorator $attachmentDecorator
     *
     * @return AttachmentProvider
     */
    public function setAttachmentDecorator(AttachmentDecorator $attachmentDecorator): self
    {
        $this->attachmentDecorator = $attachmentDecorator;

        return $this;
    }

    /**
     * @param ActionProviderInterface $action
     *
     * @return BasicActions
     */
    public function addAction(ActionProviderInterface $action, string $alias): self
    {
        $action->setAlias($alias);
        $this->actions[$alias] = $action;

        return $this;
    }

    /**
     * @param Signal $signal
     *
     * @return Action[]
     */
    public function buildActions(Signal $signal): array
    {
        $actions = [];
        foreach ($this->actions as $index => $actionProvider) {
            /* @var ActionProvider $actionProvider */
            $action = $actionProvider->getAction($signal, (int) $index);
            if (null !== $action) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($alias): bool
    {
        return substr($alias, 0, \strlen($this->getAlias())) === $this->getAlias();
    }

    public function execute(InteractiveMessage $message): Attachment
    {
        $action = $message->getAction();
        foreach ($this->actions as $provider) {
            /** @var ActionProviderInterface $provider */
            if ($provider->supports($action->getName())) {
                return $provider->execute($message);
            }
        }

        throw new \RuntimeException("No Action Provider supports '{$action->getName()}'.");
    }
}
