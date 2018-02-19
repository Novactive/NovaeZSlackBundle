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

namespace Novactive\Bundle\eZSlackBundle\Repository;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query as eZQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\User\User as ValueUser;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Class User.
 */
class User
{
    const SLACK_ID      = 'novaezslack_id';
    const SLACK_TEAM_ID = 'novaezslack_teamid';

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * User constructor.
     *
     * @param Repository              $repository
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(Repository $repository, ConfigResolverInterface $configResolver)
    {
        $this->repository     = $repository;
        $this->configResolver = $configResolver;
    }

    /**
     * @param string $id
     * @param string $teamId
     *
     * @return ValueUser|null
     */
    public function findBySlackIds(string $id, string $teamId): ?ValueUser
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($id, $teamId) {
                $searchService = $repository->getSearchService();
                $query         = new eZQuery();
                $query->filter = new Criterion\LogicalAnd(
                    [
                        new Criterion\ContentTypeIdentifier(
                            $this->configResolver->getParameter('slackconnect_contenttype_identifier', 'nova_ezslack')
                        ),
                        new Criterion\Field(self::SLACK_ID, Criterion\Operator::EQ, $id),
                        new Criterion\Field(
                            self::SLACK_TEAM_ID,
                            Criterion\Operator::EQ,
                            $teamId
                        ),
                    ]
                );
                $result        = $searchService->findContent($query);

                return $result->totalCount >= 1 ? $repository->getUserService()->loadUser(
                    $result->searchHits[0]->valueObject->id
                ) : null;
            }
        );
    }
}
