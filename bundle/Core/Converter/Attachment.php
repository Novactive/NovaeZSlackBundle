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
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\FieldType\RichText\Converter as RichTextConverter;
use eZ\Publish\Core\FieldType\RichText\Value as RichTextValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment as AttachmentModel;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Author;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Field;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Attachment.
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
     * Content constructor.
     *
     * @param Repository              $repository
     * @param RichTextConverter       $converter
     * @param UrlGeneratorInterface   $router
     * @param ConfigResolverInterface $configResolver
     * @param array                   $siteAccessList
     */
    public function __construct(
        Repository $repository,
        RichTextConverter $converter,
        UrlGeneratorInterface $router,
        ConfigResolverInterface $configResolver,
        array $siteAccessList
    ) {
        $this->repository        = $repository;
        $this->richTextConverter = $converter;
        $this->siteAccessList    = $siteAccessList;
        $this->router            = $router;
        $this->configResolver    = $configResolver;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private function getParameter($name)
    {
        return $this->configResolver->getParameter($name, 'nova_ezslack');
    }

    /**
     * @param int $id
     *
     * @return Content
     */
    private function findContent(int $id): Content
    {
        $contentService = $this->repository->getContentService();

        return $contentService->loadContent($id);
    }

    /**
     * @param int $contentId
     *
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

    /**
     * @param int $contentId
     *
     * @return AttachmentModel
     */
    public function getMain(int $contentId): AttachmentModel
    {
        $content    = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $attachment->setAuthor($this->getAuthor($content));
        $attachment->setTitle($this->sanitize($content->contentInfo->name));
        $attachment->setFallback($this->sanitize($content->contentInfo->name));
        $attachment->setText($this->getDescription($content));
        $attachment->setColor($this->getParameter('styles')['attachment']['content']);
        $attachment->setFooter($this->getParameter('site_name'));
        $attachment->setFooterIcon($this->getParameter('favicon'));
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) use ($content) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId !== $mediaSection->id) {
            $attachment->setThumbURL($this->getPictureUrl($content));
        }

        return $attachment;
    }

    /**
     * @param int $contentId
     *
     * @return AttachmentModel
     */
    public function getDetails(int $contentId): AttachmentModel
    {
        $content    = $this->findContent($contentId);
        $attachment = new AttachmentModel();
        $attachment->setColor($this->getParameter('styles')['attachment']['details']);
        $fields = [
            new Field(
                '_t:field.content.published',
                $this->formatDate($content->contentInfo->publishedDate)
            ),
            new Field(
                '_t:field.content.modified',
                $this->formatDate($content->contentInfo->modificationDate)
            ),
            new Field('_t:field.content.id', (string) $content->id),
            new Field(
                '_t:field.content.version',
                (string) $content->contentInfo->currentVersionNo
            ),
        ];

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
        $allGroups          = $objectStateService->loadObjectStateGroups();
        foreach ($allGroups as $group) {
            if ('ez_lock' === $group->identifier) {
                continue;
            }
            $state    = $this->repository->getObjectStateService()->getContentState($content->contentInfo, $group);
            $fields[] = new Field($group->getName($group->mainLanguageCode), $state->getName($state->mainLanguageCode));
        }

        if ($content->contentInfo->published) {
            $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
            foreach ($locations as $location) {
                foreach ($this->siteAccessList as $siteAccessName) {
                    $url       = $this->router->generate(
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

        return $attachment;
    }

    /**
     * @param int $contentId
     *
     * @return AttachmentModel|null
     */
    public function getPreview(
        int $contentId
    ): ?AttachmentModel {
        $content      = $this->findContent($contentId);
        $mediaSection = $this->repository->sudo(
            function (Repository $repository) use ($content) {
                return $repository->getSectionService()->loadSectionByIdentifier('media');
            }
        );
        if ($content->contentInfo->sectionId === $mediaSection->id) {
            $attachment = new AttachmentModel();
            $attachment->setColor($this->getParameter('styles')['attachment']['preview']);
            $attachment->setImageURL($this->getPictureUrl($content));

            return $attachment;
        }

        return null;
    }

    /**
     * @param ValueContent $content
     *
     * @return null|string
     */
    private function getDescription(
        ValueContent $content
    ): ?string {
        $fieldIdentifiers = $this->getParameter('field_identifiers')['description'];
        foreach ($fieldIdentifiers as $try) {
            $value = $content->getFieldValue($try);
            if (null === $value) {
                continue;
            }
            if ($value instanceof RichTextValue) {
                return $this->sanitize($this->richTextConverter->convert($value->xml)->saveHTML());
            }
            if (isset($value->text)) {
                return $this->sanitize(($value->text));
            }
        }

        return null;
    }

    /**
     * @param ValueContent $content
     *
     * @return null|string
     */
    private function getPictureUrl(
        ValueContent $content
    ): ?string {
        $fieldIdentifiers = $this->getParameter('field_identifiers')['image'];
        foreach ($fieldIdentifiers as $try) {
            $value = $content->getFieldValue($try);
            if (null !== $value && $value instanceof ImageValue) {
                return ($this->getParameter('asset_prefix') ?? '').$value->uri;
            }
            if (null !== $value && $value instanceof RelationListValue && count($value->destinationContentIds) > 0) {
                $image = $this->repository->getContentService()->loadContent($value->destinationContentIds[0]);

                return $this->getPictureUrl($image);
            }
            if (null !== $value && $value instanceof RelationValue && $value->destinationContentId > 0) {
                $image = $this->repository->getContentService()->loadContent($value->destinationContentId);

                return $this->getPictureUrl($image);
            }
        }

        return null;
    }

    /**
     * @param ValueContent $content
     *
     * @return Author
     */
    private function getAuthor(
        ValueContent $content
    ): Author {
        return $this->repository->sudo(
            function (Repository $repository) use ($content) {
                $contentService = $repository->getContentService();
                $owner          = $contentService->loadContent($content->contentInfo->ownerId);
                $author         = new Author($this->sanitize($owner->contentInfo->name));
                $author->setIcon($this->getPictureUrl($owner));

                return $author;
            }
        );
    }

    /**
     * @param null|string $text
     *
     * @return string
     */
    private function sanitize(
        ?string $text
    ): string {
        return trim(strip_tags(html_entity_decode($text)));
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    private function formatDate(
        DateTime $dateTime
    ): string {
        return $dateTime->format(DateTime::RFC850);
    }
}
