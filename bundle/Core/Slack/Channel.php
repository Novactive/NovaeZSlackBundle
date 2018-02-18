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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Channel.
 */
class Channel
{
    /**
     * A string identifier for the channel housing the originating message. Channel IDs are unique to the workspace
     * they appear within.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * The name of the channel the message appeared in, without the leading # character.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Channel
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Channel
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
