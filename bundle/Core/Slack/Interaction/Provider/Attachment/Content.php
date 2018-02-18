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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment;

use eZ\Publish\Core\SignalSlot\Signal;
use Novactive\Bundle\eZSlackBundle\Core\Converter\Attachment as AttachmentConverter;
use Novactive\Bundle\eZSlackBundle\Core\Signal\Searched as SearchedSignal;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;

/**
 * Class Content.
 */
class Content extends AttachmentProvider
{
    /**
     * @var AttachmentConverter
     */
    private $converter;

    /**
     * Content constructor.
     *
     * @param AttachmentConverter $converter
     */
    public function __construct(AttachmentConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment(Signal $signal): ?Attachment
    {
        if (isset($signal->contentId)) {
            if ('novaezslack.provider.main' === $this->getAlias()) {
                return $this->converter->getMain((int) $signal->contentId);
            }

            if ('novaezslack.provider.details' === $this->getAlias() && !$signal instanceof SearchedSignal) {
                return $this->converter->getDetails((int) $signal->contentId);
            }
            if ('novaezslack.provider.preview' === $this->getAlias() && !$signal instanceof SearchedSignal) {
                return $this->converter->getPreview((int) $signal->contentId);
            }
        }

        return null;
    }
}
