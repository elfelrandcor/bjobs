<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Console;

use JuriyPanasevich\BJobs\QueueListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListenCommand extends Command {

    protected function configure() {
        $this
            ->setName('listen')
            ->setDescription('Start listener')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Queue name', 'application')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getOption('name');
        $output->writeln('Start process...');
        $output->writeln(sprintf('Queue name `%s`', $name));

        $listener = new QueueListener();
        $listener->listen($name);
    }
}