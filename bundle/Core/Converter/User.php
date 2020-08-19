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

use AdamPaterson\OAuth2\Client\Provider\SlackResourceOwner;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\User\User as ValueUser;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Novactive\Bundle\eZSlackBundle\Repository\User as UserRepository;
use RuntimeException;

/**
 * Class User.
 */
class User
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var array
     */
    private $languages;

    /**
     * User constructor.
     */
    public function __construct(
        Repository $repository,
        UserRepository $userRepository,
        ConfigResolverInterface $configResolver
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->configResolver = $configResolver;
        $this->languages = $configResolver->getParameter('languages');
    }

    /**
     * @param $paramName
     */
    private function getParameter($paramName)
    {
        return $this->configResolver->getParameter($paramName, 'nova_ezslack');
    }

    /**
     * Add 2 string to User Content Type to store Slack IDs (id and team id).
     */
    private function checkAndCreateFieldDefinition(): void
    {
        $contentTypeService = $this->repository->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier(
            $this->getParameter('slackconnect_contenttype_identifier')
        );
        $existingFields = [];
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            $existingFields[] = $fieldDefinition->identifier;
        }
        if (
            \in_array(UserRepository::SLACK_ID, $existingFields) &&
            \in_array(UserRepository::SLACK_TEAM_ID, $existingFields)
        ) {
            return;
        }

        try {
            $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);
        } catch (BadStateException $e) {
            $contentTypeDraft = $contentTypeService->loadContentTypeDraft($contentType->id);
        }
        if (!\in_array(UserRepository::SLACK_ID, $existingFields)) {
            $fieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
                UserRepository::SLACK_ID,
                'ezstring'
            );
            $fieldCreateStruct->isSearchable = true;
            $fieldCreateStruct->isTranslatable = false;
            $fieldCreateStruct->isRequired = false;
            foreach ($this->languages as $lang) {
                $fieldCreateStruct->names[$lang] = 'Slack ID';
            }
            $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldCreateStruct);
        }
        if (!\in_array(UserRepository::SLACK_TEAM_ID, $existingFields)) {
            $fieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
                UserRepository::SLACK_TEAM_ID,
                'ezstring'
            );
            $fieldCreateStruct->isSearchable = true;
            $fieldCreateStruct->isTranslatable = false;
            $fieldCreateStruct->isRequired = false;
            foreach ($this->languages as $lang) {
                $fieldCreateStruct->names[$lang] = 'Slack Team ID';
            }
            $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldCreateStruct);
        }
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    private function updateUser(SlackResourceOwner $resource, ValueUser $user): ValueUser
    {
        $slackId = $resource->getId();
        $slackTeamId = $resource->getProfile()['team'];
        if (
            $user->getFieldValue(UserRepository::SLACK_ID)->text === $slackId &&
            $user->getFieldValue(UserRepository::SLACK_TEAM_ID)->text === $slackTeamId
        ) {
            return $user;
        }
        $attributes = [
            UserRepository::SLACK_ID => $slackId,
            UserRepository::SLACK_TEAM_ID => $slackTeamId,
        ];
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $this->getParameter('slackconnect_contenttype_identifier')
        );
        $contentService = $this->repository->getContentService();
        $userService = $this->repository->getUserService();
        $userUpdateStruct = $userService->newUserUpdateStruct();
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        foreach ($contentType->getFieldDefinitions() as $field) {
            /* @var FieldDefinition $field */
            $fieldName = $field->identifier;
            if (!array_key_exists($fieldName, $attributes)) {
                continue;
            }
            $fieldValue = $attributes[$fieldName];
            $contentUpdateStruct->setField($fieldName, $fieldValue);
        }
        $userUpdateStruct->contentUpdateStruct = $contentUpdateStruct;
        $user = $userService->updateUser($user, $userUpdateStruct);

        $draft = $contentService->createContentDraft($user->contentInfo);
        $content = $contentService->publishVersion($draft->versionInfo);

        return $userService->loadUser($content->id);
    }

    /**
     * @SuppressWarnings(PHPMD.UndefinedVariable)
     */
    private function createUser(SlackResourceOwner $resource): ValueUser
    {
        [$first, $last] = explode(' ', $resource->getRealName(), 2);
        $attributes = [
            'last_name' => $first,
            'first_name' => $last,
            'signature' => $resource->getProfile()['title'] ?? '',
            UserRepository::SLACK_ID => $resource->getId(),
            UserRepository::SLACK_TEAM_ID => $resource->getProfile()['team'],
        ];

        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $this->getParameter('slackconnect_contenttype_identifier')
        );

        $userService = $this->repository->getUserService();
        $userCreateStruct = $userService->newUserCreateStruct(
            $resource->getEmail(),
            $resource->getEmail(),
            md5(uniqid($resource->getId(), true)),
            $this->languages[0],
            $contentType
        );
        foreach ($contentType->getFieldDefinitions() as $field) {
            /* @var FieldDefinition $field */
            $fieldName = $field->identifier;
            if (!array_key_exists($fieldName, $attributes)) {
                continue;
            }
            $fieldValue = $attributes[$fieldName];
            $userCreateStruct->setField($fieldName, $fieldValue);
        }
        $group = $userService->loadUserGroup($this->getParameter('slackconnect_usergroup_content_id'));
        $user = $userService->createUser($userCreateStruct, [$group]);

        $contentService = $this->repository->getContentService();
        $draft = $contentService->createContentDraft($user->contentInfo);
        $content = $contentService->publishVersion($draft->versionInfo);

        return $userService->loadUser($content->id);
    }

    public function convert(ResourceOwnerInterface $resource): ValueUser
    {
        if (!$resource instanceof SlackResourceOwner) {
            throw new RuntimeException('User Converter works only with SlackResourceOwner.');
        }

        return $this->repository->sudo(
            function (Repository $repository) use ($resource) {
                $this->checkAndCreateFieldDefinition();
                $userEmail = $resource->getEmail();
                $userService = $repository->getUserService();
                $existingUser = $this->userRepository->findBySlackIds(
                    (string) $resource->getId(),
                    (string) $resource->getProfile()['team']
                );

                if (null === $existingUser) {
                    try {
                        $existingUser = $userService->loadUserByLogin($userEmail);
                    } catch (NotFoundException $e) {
                        if (null === $existingUser) {
                            $existingUser = $userService->loadUsersByEmail($userEmail);
                            $existingUser = $existingUser[0] ?? null;
                        }
                    }
                }

                return (null === $existingUser) ? $this->createUser($resource) : $this->updateUser(
                    $resource,
                    $existingUser
                );
            }
        );
    }
}
