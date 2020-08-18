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

use Novactive\Bundle\eZSlackBundle\Core\Slack\Responder\FirstResponder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestSearchCommand.
 */
class TestSlackCommand extends Command
{
    /**
     * @var FirstResponder
     */
    private $firstResponder;

    /**
     * TestSearchCommand constructor.
     */
    public function __construct(FirstResponder $firstResponder)
    {
        $this->firstResponder = $firstResponder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('novaezslack:test:slack:command')
            ->setDescription('Test a slack command, /ez.')
            ->setHidden(true)
            ->addArgument('args', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The arguments');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $firstResponder = $this->firstResponder;
        $message = $firstResponder(implode(' ', $input->getArgument('args')));
        dump($message);
        $output->writeln('Done.');
    }
}
