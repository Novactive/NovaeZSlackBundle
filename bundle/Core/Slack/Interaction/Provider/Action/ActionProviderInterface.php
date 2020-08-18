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

/**
 * Interface ActionProviderInterface.
 */
interface ActionProviderInterface
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
     * @param int    $index
     *
     * @return Action|null
     */
    public function getAction(Signal $signal, int $index): ?Action;

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
