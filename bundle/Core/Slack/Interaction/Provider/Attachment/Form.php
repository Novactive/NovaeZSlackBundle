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
use EzSystems\FormBuilder\API\Repository\Values\SubmissionField;
use EzSystems\FormBuilder\Core\Repository\FormService;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Field;

/**
 * Class Form.
 */
class Form extends AttachmentProvider
{
    /**
     * @var FormService
     */
    private $formService;

    /**
     * Form constructor.
     *
     * @param FormService $formService
     */
    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment(Signal $signal): ?Attachment
    {
        if (class_exists(\EzSystems\FormBuilder\Core\SignalSlot\Signal\FormSubmit::class) &&
            !$signal instanceof \EzSystems\FormBuilder\Core\SignalSlot\Signal\FormSubmit) {
            return null;
        }
        $form       = $this->formService->loadForm($signal->formId);
        $attachment = new Attachment();
        $attachment->setTitle($form->name.' - FormId:'.$form->id);
        $attachment->setText($form->description);
        $attachment->setCallbackId($this->getAlias().'.'.time());
        foreach ($signal->submission->fields as $field) {
            /* @var SubmissionField $field */
            $attachment->addField(new Field($field->label, $field->value, \strlen($field->value) <= 50));
        }

        $this->attachmentDecorator->addAuthor($attachment, $form->user->id);
        $this->attachmentDecorator->decorate($attachment, 'form');
        $this->attachmentDecorator->addSiteInformation($attachment);

        return $attachment;
    }
}
