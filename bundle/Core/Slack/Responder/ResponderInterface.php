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
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get the Help.
     *
     * @return string
     */
    public function getHelp(): string;

    /**
     * Invoke the Responder.
     *
     * @param array $arguments The Arguments
     *
     * @return Message
     */
    public function respond(array $arguments = []): Message;
}
