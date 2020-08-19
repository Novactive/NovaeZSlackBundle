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

namespace Novactive\Bundle\eZSlackBundle\Core\Converter;

use DateTime;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\Core\FieldType\RichText\Converter as RichTextConverter;
use eZ\Publish\Core\FieldType\RichText\Value as RichTextValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZSlackBundle\Core\Decorator\Attachment as AttachmentDecorator;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment as AttachmentModel;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Field;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @SuppressWarnings(PHPMD.IfStatementAssignment)
 */
class Attachment
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var RichTextConverter
     */
    private $richTextConverter;

    /**
     * @var array
     */
    private $siteAccessList;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var AttachmentDecorator
     */
    private $attachmentDecorator;

    /**
     * Attachment constructor.
     */
    public function __construct(
        Repository $repository,
        RichTextConverter $converter,
        UrlGeneratorInterface $router,
        ConfigResolverInterface $configResolver,
        AttachmentDecorator $decorator,
        array $siteAccessList
    ) {
        $this->repository = $repository;
        $this->richTextConverter = $converter;
        $this->siteAccessList = $siteAccessList;
        $this->router = $router;
        $this->attachmentDecorator = $decorator;
        $this->configResolver = $configResolver;
    }

    /**
     * @param $name
     */
    private function getParameter($name)
    {
        return $this->configResolver->getParameter($name, 'nova_ezslack');
    }

    private function findContent(int $id): Content
    {
        $contentService = $this->repository->getContentService();

        return $contentService->loadContent($id);
    }

    /**
     * @return Attachment[]
     */
    public function convert(int $contentId): array
    {
        $attachments = [
            $this->getMain($contentId),
            $this->getDetails($contentId),
        ];

        if ($attachment = $this->getPreview($contentId)) {
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    public function getMain(int $contentId): AttachmentModel
    {
        $content = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $this->attachmentDecorator->addAuthor($attachment, $content->contentInfo->ownerId);
        $attachment->setTitle($content->contentInfo->name);
        $attachment->setText($this->getDescription($content));
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId !== $mediaSection->id) {
            $attachment->setThumbURL($this->attachmentDecorator->getPictureUrl($content));
        }
        $this->attachmentDecorator->decorate($attachment);
        $this->attachmentDecorator->addSiteInformation($attachment);

        return $attachment;
    }

    public function getDetails(int $contentId): AttachmentModel
    {
        $content = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $fields = [];
        if (null !== $content->contentInfo->publishedDate) {
            $fields[] = new Field(
                '_t:field.content.published',
                $this->formatDate($content->contentInfo->publishedDate)
            );
        }
        if (null !== $content->contentInfo->modificationDate) {
            $fields[] = new Field(
                '_t:field.content.modified',
                $this->formatDate($content->contentInfo->modificationDate)
            );
        }

        $fields[] = new Field('_t:field.content.id', (string) $content->id);
        $fields[] = new Field(
            '_t:field.content.version',
            (string) $content->contentInfo->currentVersionNo
        );

        if ($content->contentInfo->mainLocationId > 0) {
            $fields[] = new Field(
                '_t:field.content.mainlocationid',
                (string) $content->contentInfo->mainLocationId
            );
        }
        $fields[] = new Field(
            '_t:field.content.languages',
            implode(',', $content->versionInfo->languageCodes)
        );

        // states
        $objectStateService = $this->repository->getObjectStateService();
        $allGroups = $objectStateService->loadObjectStateGroups();
        foreach ($allGroups as $group) {
            if ('ez_lock' === $group->identifier) {
                continue;
            }
            $state = $this->repository->getObjectStateService()->getContentState($content->contentInfo, $group);
            $fields[] = new Field($group->getName($group->mainLanguageCode), $state->getName($state->mainLanguageCode));
        }

        if ($content->contentInfo->published) {
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                foreach ($this->siteAccessList as $siteAccessName) {
                    $url = $this->router->generate(
                        $location,
                        [
                            'siteaccess' => $siteAccessName,
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    $fieldName = "SiteAccess {$siteAccessName}";
                    if ($location->id !== $content->contentInfo->mainLocationId) {
                        $fieldName = "Location: {$location->id} {$fieldName}";
                    }
                    $fields[] = new Field($fieldName, explode('?', $url)[0], false);
                }
            }
        }
        $attachment->setFields($fields);
        $this->attachmentDecorator->decorate($attachment, 'details');

        return $attachment;
    }

    public function getPreview(int $contentId): ?AttachmentModel
    {
        $content = $this->findContent($contentId);
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId === $mediaSection->id) {
            $attachment = new AttachmentModel();
            $attachment->setImageURL($this->attachmentDecorator->getPictureUrl($content));
            $this->attachmentDecorator->decorate($attachment, 'preview');

            return $attachment;
        }

        return null;
    }

    private function getDescription(ValueContent $content): ?string
    {
        $fieldIdentifiers = $this->getParameter('field_identifiers')['description'];
        foreach ($fieldIdentifiers as $try) {
            $value = $content->getFieldValue($try);
            if (null === $value) {
                continue;
            }
            if ($value instanceof RichTextValue) {
                return $this->richTextConverter->convert($value->xml)->saveHTML();
            }
            if (isset($value->text)) {
                return $value->text;
            }
        }

        return null;
    }

    private function formatDate(DateTime $dateTime): string
    {
        return $dateTime->format(DateTime::RFC850);
    }
}
