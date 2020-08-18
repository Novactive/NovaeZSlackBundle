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
 * Class User.
 */
class User
{
    /**
     * A string identifier for the user invoking the action. Users IDs are unique to the workspace they appear within.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * The name of that very same user.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return User
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
