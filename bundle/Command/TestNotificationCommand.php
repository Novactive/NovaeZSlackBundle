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

use eZ\Publish\Core\SignalSlot\Signal\ContentService\PublishVersionSignal;
use Novactive\Bundle\eZSlackBundle\Core\Dispatcher;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestNotificationCommand.
 */
class TestNotificationCommand extends Command
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * TestNotificationCommand constructor.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('novaezslack:test:notification:message')
            ->setDescription('Convert a Content and send the notification in the channel(s).')
            ->setHidden(true)
            ->addArgument('contentId', InputArgument::OPTIONAL, 'ContentId', 1);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contentId = (int) $input->getArgument('contentId');
        $slot      = new PublishVersionSignal(['contentId' => $contentId, 'versionNo' => 1]);
        $this->dispatcher->receive($slot);
        $output->writeln("Dispatch {$contentId} Done.");
    }
}
