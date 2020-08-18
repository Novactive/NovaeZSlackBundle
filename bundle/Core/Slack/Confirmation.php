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
 * Class Confirmation.
 */
class Confirmation
{
    /**
     * Title the pop up window. Please be brief.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $title;

    /**
     * Describe in detail the consequences of the action and contextualize your button text choices.
     * Use a maximum of 30 characters or so for best results across form factors.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $text;

    /**
     * The text label for the button to continue with an action. Keep it short. Defaults to Okay.
     *
     * @var string
     * @Serializer\SerializedName("ok_text")
     * @Serializer\Type("string<translatable>")
     */
    private $okText;

    /**
     * The text label for the button to cancel the action. Keep it short. Defaults to Cancel.
     *
     * @var string
     * @Serializer\SerializedName("dismiss_text")
     * @Serializer\Type("string<translatable>")
     */
    private $dismissText;

    /**
     * Confirmation constructor.
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return Confirmation
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Confirmation
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getOkText(): ?string
    {
        return $this->okText;
    }

    /**
     * @return Confirmation
     */
    public function setOkText(string $okText): self
    {
        $this->okText = $okText;

        return $this;
    }

    /**
     * @return string
     */
    public function getDismissText(): ?string
    {
        return $this->dismissText;
    }

    /**
     * @return Confirmation
     */
    public function setDismissText(string $dismissText): self
    {
        $this->dismissText = $dismissText;

        return $this;
    }
}
