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
 * Class Message.
 */
class Message
{
    /**
     * @var string
     * @Serializer\Type("string<translatable>")
     */
    private $text;

    /**
     * @var bool
     * @Serializer\Type("boolean")
     */
    private $markdown;

    /**
     * @var Attachment[]
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment>")
     */
    private $attachments;

    /**
     * Message constructor.
     *
     * @param null|string $text
     * @param array       $attachements
     */
    public function __construct(?string $text = null, array $attachements = [])
    {
        $this->text = $text;
        $this->setAttachments($attachements);
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
     * @return Message
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param Attachment $attachment
     *
     * @return $this
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @param $index
     *
     * @return Message
     */
    public function removeAttachmentAtIndex($index): self
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);

        return $this;
    }

    /**
     * @param array $attachments
     *
     * @return Message
     */
    public function setAttachments(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            if (!$attachment instanceof Attachment) {
                throw new \RuntimeException(sprintf('Provided Attachment is not an %s', Attachment::class));
            }
        }
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMarkdown(): bool
    {
        return $this->markdown;
    }

    /**
     * @param bool $markdown
     *
     * @return Message
     */
    public function setMarkdown(bool $markdown): self
    {
        $this->markdown = $markdown;

        return $this;
    }
}
