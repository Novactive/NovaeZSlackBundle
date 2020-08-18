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
 * Class Team.
 */
class Team
{
    /**
     * A unique identifier for the Slack workspace where the originating message appeared.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * The slack.com subdomain of that same Slack workspace, like watermelonsugar.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $domain;

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
     * @return Team
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Team
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }
}
