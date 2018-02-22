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
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\AliasTrait;

/**
 * Class ActionProvider.
 */
abstract class ActionProvider implements ActionProviderInterface
{
    use AliasTrait;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @required
     *
     * @param Repository $repository
     *
     * @return ActionProvider
     */
    public function setRepository(Repository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($alias): bool
    {
        return substr($alias, 0, \strlen($this->getAlias())) === $this->getAlias();
    }

    /**
     * @param Signal $signal
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    protected function getContentForSignal(Signal $signal): ?Content
    {
        if (isset($signal->contentId)) {
            return $this->repository->getContentService()->loadContent($signal->contentId);
        }
        if (isset($signal->data['content_id'])) {
            return $this->repository->getContentService()->loadContent($signal->data['content_id']);
        }
        if (isset($signal->data['contentId'])) {
            return $this->repository->getContentService()->loadContent($signal->data['contentId']);
        }

        return null;
    }
}
