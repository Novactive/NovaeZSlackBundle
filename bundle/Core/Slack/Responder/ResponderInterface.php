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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Responder;

use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;

/**
 * Interface ResponderInterface.
 */
interface ResponderInterface
{
    /**
     * Get the name.
     */
    public function getName(): string;

    /**
     * Get the description.
     */
    public function getDescription(): string;

    /**
     * Get the Help.
     */
    public function getHelp(): string;

    /**
     * Invoke the Responder.
     *
     * @param array $arguments The Arguments
     */
    public function respond(array $arguments = []): Message;
}
