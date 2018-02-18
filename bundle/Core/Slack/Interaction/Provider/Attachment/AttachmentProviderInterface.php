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
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;

/**
 * Interface AttachmentProviderInterface.
 */
interface AttachmentProviderInterface
{
    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void;

    /**
     * @return string
     */
    public function getAlias(): string;

    /**
     * @param Signal $signal
     *
     * @return Attachment|null
     */
    public function getAttachment(Signal $signal): ?Attachment;

    /**
     * @param $alias
     *
     * @return bool
     */
    public function supports($alias): bool;

    /**
     * @param InteractiveMessage $message
     *
     * @return Attachment
     */
    public function execute(InteractiveMessage $message): Attachment;
}
