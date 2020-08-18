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
 * Class Field.
 */
class Field
{
    /**
     * Shown as a bold heading above the value text. It cannot contain markup and will be escaped for you.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $title;

    /**
     * The text value of the field. It may contain standard message markup and must be escaped as normal.
     * May be multi-line.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * An optional flag indicating whether the value is short enough to be displayed side-by-side with other values.
     *
     * @var bool
     * @Serializer\Type("bool")
     */
    private $short;

    /**
     * Field constructor.
     *
     * @param string    $title
     * @param string    $value
     * @param bool|null $short
     */
    public function __construct(string $title, string $value, ?bool $short = true)
    {
        $this->title = $title;
        $this->value = $value;
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Field
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Field
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShort(): bool
    {
        return $this->short;
    }

    /**
     * @param bool $short
     *
     * @return Field
     */
    public function setShort(bool $short): self
    {
        $this->short = $short;

        return $this;
    }
}
