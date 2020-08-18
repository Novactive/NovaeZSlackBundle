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
 * Class Option.
 */
class Option
{
    /**
     * A short, user-facing string to label this option to users. Use a maximum of 30 characters or so for best results
     * across, you guessed it, form factors.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $text;

    /**
     * A short string that identifies this particular option to your application. It will be sent to your Action URL
     * when this option is selected. While there's no limit to the value of your Slack app, this value may contain up
     * to only 2000 characters.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * A user-facing string that provides more details about this option. Also should contain up to 30 characters.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $description;

    /**
     * Option constructor.
     */
    public function __construct(string $text, string $value)
    {
        $this->text = $text;
        $this->value = $value;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Option
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return Option
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Option
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
