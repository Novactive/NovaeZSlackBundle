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
 * Class Action.
 *
 * @Serializer\Discriminator(field = "type", disabled = false, map = {"button":
 *                                 "Novactive\Bundle\eZSlackBundle\Core\Slack\Button", "select":
 *                                 "Novactive\Bundle\eZSlackBundle\Core\Slack\Select"})
 */
abstract class Action
{
    /**
     * Provide a string to give this specific action a name. The name will be returned to your Action URL along with
     * the message's callback_id when this action is invoked. Use it to identify this particular response path.
     * If multiple actions share the same name, only one of them can be in a triggered state.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * The user-facing label for the message button or menu representing this action. Cannot contain markup.
     * Best to keep these short and decisive. Use a maximum of 30 characters or so for best results across form factors.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $text;

    /**
     * Provide button when this action is a message button or provide select when the action is a message menu.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $value;

    /**
     * an array of option value hashes selected by the user from this message menu.
     *
     * @var string
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Option>")
     * @Serializer\SerializedName("selected_options")
     */
    private $selectedOptions;

    /**
     * @var Confirmation
     * @Serializer\SerializedName("confirm")
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\Confirmation")
     */
    private $confirmation;

    /**
     * Action constructor.
     *
     * @param string $name
     * @param string $text
     * @param string $value
     */
    public function __construct(string $name, string $text, string $value)
    {
        $this->name  = $name;
        $this->text  = $text;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Action
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Action
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Action
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Confirmation
     */
    public function getConfirmation(): ?Confirmation
    {
        return $this->confirmation;
    }

    /**
     * @param Confirmation $confirmation
     *
     * @return Action
     */
    public function setConfirmation(Confirmation $confirmation): self
    {
        if (null === $confirmation->getTitle()) {
            $confirmation->setTitle($this->getText());
        }
        $this->confirmation = $confirmation;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedOptions(): string
    {
        return $this->selectedOptions;
    }

    /**
     * @return Option|null
     */
    public function getSelectedOption(): ?Option
    {
        return $this->selectedOptions[0];
    }

    /**
     * @param string $selectedOptions
     *
     * @return Action
     */
    public function setSelectedOptions(string $selectedOptions): self
    {
        $this->selectedOptions = $selectedOptions;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("type")
     *
     * @return string
     */
    abstract public function getObjectType(): string;
}
