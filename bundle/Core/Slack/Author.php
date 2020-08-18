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
 * Class Author.
 */
class Author
{
    /**
     * Small text used to display the author's name.
     *
     * @var string
     * @Serializer\SerializedName("author_name")
     */
    private $name;

    /**
     * A valid URL that will hyperlink the author_name text mentioned above. Will only work if author_name is present.
     *
     * @var string
     * @Serializer\SerializedName("author_link")
     */
    private $link;

    /**
     * A valid URL that displays a small 16x16px image to the left of the author_name text. Will only work
     * if author_name is present.
     *
     * @var string
     * @Serializer\SerializedName("author_icon")
     */
    private $icon;

    /**
     * Author constructor.
     */
    public function __construct(?string $name = null, ?string $link = null, ?string $icon = null)
    {
        $this->name = $name;
        $this->link = $link;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(?string $icon = null): self
    {
        $this->icon = $icon;

        return $this;
    }
}
