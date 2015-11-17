<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Console;

use JuriyPanasevich\BJobs\Journal\QueueJournal;
use JuriyPanasevich\BJobs\QueueWorker;
use JuriyPanasevich\BJobs\RedisQueue;
use JuriyPanasevich\Logger\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkCommand extends Command {

    protected function configure() {
        $this
            ->setName('work')
            ->setDescription('Run worker')
            ->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'Queue name', 'application')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'delay')
            ->addOption('memory', null, InputOption::VALUE_OPTIONAL, 'memory limit')
            ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'sec to sleep')
            ->addOption('tries', null, InputOption::VALUE_OPTIONAL, 'maximum tries')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Start process...');
        $queue = new RedisQueue($input->getOption('queue'));

        $journal = new QueueJournal();
        $journal->setEntity($queue);

        $logger = new Logger($journal);

        $worker = new QueueWorker($queue, $logger);
        $worker->run($input->getOption('delay'), $input->getOption('memory'), $input->getOption('sleep'), $input->getOption('tries'));
    }
}