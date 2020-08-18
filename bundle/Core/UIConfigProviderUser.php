<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license   For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Novactive\Bundle\eZSlackBundle\Core;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\User\User as ApiUser;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Provides information about current user with resolved profile picture.
 * HERE ONLY TO FIX A BUG: https://github.com/ezsystems/ezplatform-admin-ui/pull/353.
 */
class UIConfigProviderUser implements ProviderInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ContentTypeService    $contentTypeService
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ContentTypeService $contentTypeService
    ) {
        $this->tokenStorage       = $tokenStorage;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Returns configuration structure compatible with PlatformUI.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $config = ['user' => null, 'profile_picture_field' => null];
        $token  = $this->tokenStorage->getToken();

        if ($token instanceof UsernamePasswordToken || $token instanceof PostAuthenticationGuardToken) {
            $user    = $token->getUser();
            $apiUser = $user->getAPIUser();

            $config['user']                  = $apiUser;
            $config['profile_picture_field'] = $this->resolveProfilePictureField($apiUser);
        }

        return $config;
    }

    /**
     * Returns first occurrence of an `ezimage` fieldtype.
     *
     * @param ApiUser $user
     *
     * @return Field|null
     */
    private function resolveProfilePictureField(ApiUser $user): ?Field
    {
        try {
            $contentType = $this->contentTypeService->loadContentType($user->contentInfo->contentTypeId);
        } catch (\Exception $e) {
            return null;
        }

        foreach ($user->getFields() as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            if ('ezimage' === $fieldDef->fieldTypeIdentifier) {
                return $field;
            }
        }

        return null;
    }
}
