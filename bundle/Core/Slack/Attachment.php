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
use RuntimeException;

/**
 * Class Attachment.
 */
class Attachment
{
    /**
     * A plain-text summary of the attachment. This text will be used in clients that don't show
     * formatted text (eg. IRC, mobile notifications) and should not contain any markup.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $fallback;

    /**
     * The provided string will act as a unique identifier for the collection of buttons within the attachment.
     * It will be sent back to your message button action URL with each invoked action. This field is required when
     * the attachment contains message buttons. It is key to identifying the interaction you're working with.
     *
     * @Serializer\SerializedName("callback_id")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $callbackId;

    /**
     * Like traffic signals, color-coding messages can quickly communicate intent and help separate them from the
     * flow of other messages in the timeline.
     *
     * An optional value that can either be one of good, warning, danger, or any hex color code (eg. #439FE0).
     * This value is used to color the border along the left side of the message attachment.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $color;

    /**
     * This is optional text that appears above the message attachment block.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $pretext;

    /**
     * The author parameters will display a small section at the top of a message attachment.
     *
     * @var Author
     * @Serializer\Inline
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\Author")
     */
    private $author;

    /**
     * The title is displayed as larger, bold text near the top of a message attachment.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $title;

    /**
     * By passing a valid URL in the title_link parameter (optional), the title text will be hyperlinked.
     *
     * @var string
     * @Serializer\SerializedName("title_link")
     * @Serializer\Type("string")
     */
    private $titleLink;

    /**
     * This is the main text in a message attachment, and can contain standard message markup. The content will
     * automatically collapse if it contains 700+ characters or 5+ linebreaks, and will display a "Show more..."
     * link to expand the content. Links posted in the text field will not unfurl.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $text;

    /**
     * Fields are defined as an array, and hashes contained within it will be displayed in a table
     * inside the message attachment.
     *
     * @var Field[]
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Field>")
     */
    private $fields;

    /**
     * A valid URL to an image file that will be displayed inside a message attachment. We currently support the
     * following formats: GIF, JPEG, PNG, and BMP.
     * Large images will be resized to a maximum width of 400px or a maximum height of 500px, while still maintaining
     * the original aspect ratio.
     *
     * @var string
     * @Serializer\SerializedName("image_url")
     * @Serializer\Type("string")
     */
    private $imageURL;

    /**
     * A valid URL to an image file that will be displayed as a thumbnail on the right side of a message attachment.
     * We currently support the following formats: GIF, JPEG, PNG, and BMP.
     * The thumbnail's longest dimension will be scaled down to 75px while maintaining the aspect ratio of the image.
     * The filesize of the image must also be less than 500 KB.
     *
     * For best results, please use images that are already 75px by 75px.
     *
     * @var string
     * @Serializer\SerializedName("thumb_url")
     * @Serializer\Type("string")
     */
    private $thumbURL;

    /**
     * Add some brief text to help contextualize and identify an attachment. Limited to 300 characters, and may be
     * truncated further when displayed to users in environments with limited screen real estate.
     *
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $footer;

    /**
     * To render a small icon beside your footer text, provide a publicly accessible URL string in the footer_icon
     * field. You must also provide a footer for the field to be recognized.
     * We'll render what you provide at 16px by 16px. It's best to use an image that is similarly sized.
     *
     * @var string
     * @Serializer\SerializedName("footer_icon")
     * @Serializer\Type("string")
     */
    private $footerIcon;

    /**
     * Does your attachment relate to something happening at a specific time?
     * By providing the ts field with an integer value in "epoch time", the attachment will display an additional
     * timestamp value as part of the attachment's footer.
     *
     * Use ts when referencing articles or happenings. Your message will have its own timestamp when published.
     *
     * @var int
     * @Serializer\SerializedName("ts")
     * @Serializer\Type("integer")
     */
    private $timestamp;

    /**
     * A collection of actions (buttons or menus) to include in the attachment. Required when using message buttons
     * or message menus. A maximum of 5 actions per attachment may be provided.
     *
     * @var Action[]
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Action>")
     */
    private $actions;

    /**
     * Attachment constructor.
     */
    public function __construct()
    {
        $this->fields = [];
        $this->actions = [];
    }

    /**
     * @return string
     */
    public function getFallback(): ?string
    {
        return $this->fallback;
    }

    /**
     * @param string $fallback
     *
     * @return Attachment
     */
    public function setFallback(?string $fallback = null): self
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Attachment
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getPretext(): ?string
    {
        return $this->pretext;
    }

    /**
     * @return Attachment
     */
    public function setPretext(string $pretext): self
    {
        $this->pretext = $pretext;

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     *
     * @return Attachment
     */
    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Attachment
     */
    public function setTitle(?string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleLink(): ?string
    {
        return $this->titleLink;
    }

    /**
     * @return Attachment
     */
    public function setTitleLink(string $titleLink): self
    {
        $this->titleLink = $titleLink;

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
     * @return Attachment
     */
    public function setText(?string $text = null): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     *
     * @return Attachment
     */
    public function setFields(array $fields): self
    {
        foreach ($fields as $field) {
            if (!$field instanceof Field) {
                throw new RuntimeException(sprintf('Provided Field is not an %s', Field::class));
            }
        }
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return $this
     */
    public function addField(Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    /**
     * @param string $imageURL
     *
     * @return Attachment
     */
    public function setImageURL(?string $imageURL): self
    {
        $this->imageURL = $imageURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbURL(): ?string
    {
        return $this->thumbURL;
    }

    /**
     * @param string $thumbURL
     *
     * @return Attachment
     */
    public function setThumbURL(?string $thumbURL): self
    {
        $this->thumbURL = $thumbURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getFooter(): ?string
    {
        return $this->footer;
    }

    /**
     * @param string $footer
     *
     * @return Attachment
     */
    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @return string
     */
    public function getFooterIcon(): ?string
    {
        return $this->footerIcon;
    }

    /**
     * @param string $footerIcon
     *
     * @return Attachment
     */
    public function setFooterIcon(?string $footerIcon): self
    {
        $this->footerIcon = $footerIcon;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * @return Attachment
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackId(): ?string
    {
        return $this->callbackId;
    }

    /**
     * @return Attachment
     */
    public function setCallbackId(string $callbackId): self
    {
        $this->callbackId = $callbackId;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     *
     * @return Attachment
     */
    public function setActions(array $actions): self
    {
        foreach ($actions as $action) {
            if (!$action instanceof Action) {
                throw new RuntimeException(sprintf('Provided Attachment is not an %s', Action::class));
            }
        }
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return Attachment
     */
    public function addAction(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }
}
