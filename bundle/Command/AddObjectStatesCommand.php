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

namespace Novactive\Bundle\eZSlackBundle\Command;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddObjectStatesCommand.
 */
class AddObjectStatesCommand extends Command
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * TestSearchCommand constructor.
     */
    public function __construct(Repository $firstResponder)
    {
        $this->repository = $firstResponder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('novaezslack:fixtures:add:object:states')
            ->setDescription('Add a publication workflow chain')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentUser = $this->repository->getPermissionResolver()->getCurrentUserReference();
        $admin = $this->repository->getUserService()->loadUser(14);
        $this->repository->getPermissionResolver()->setCurrentUserReference($admin);

        $lang = 'eng-GB';
        $objectStateService = $this->repository->getObjectStateService();
        $groupStruct = $objectStateService->newObjectStateGroupCreateStruct('publication_chain');
        $groupStruct->defaultLanguageCode = $lang;
        $groupStruct->names = ['eng-GB' => 'Publication Chain'];

        $group = $objectStateService->createObjectStateGroup($groupStruct);

        $states = [
            'waiting_for_review' => 'Waiting For Review',
            'reviewed' => 'Reviewed',
            'validated' => 'Validated',
            'published' => 'Published',
        ];

        foreach ($states as $key => $value) {
            $stateStruct = $objectStateService->newObjectStateCreateStruct($key);
            $stateStruct->defaultLanguageCode = $lang;
            $stateStruct->names = [$lang => $value];
            $objectStateService->createObjectState($group, $stateStruct);
        }
        $this->repository->getPermissionResolver()->setCurrentUserReference($currentUser);
        $output->writeln('Done.');
    }
}
