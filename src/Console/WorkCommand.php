<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Console;

use JuriyPanasevich\BJobs\Journal\QueueFileJournal;
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
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'delay', 2)
            ->addOption('memory', null, InputOption::VALUE_OPTIONAL, 'memory limit', 128)
            ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'sec to sleep', 3)
            ->addOption('tries', null, InputOption::VALUE_OPTIONAL, 'maximum tries', 0)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Start process...');
        $queue = new RedisQueue($input->getOption('queue'));

        $journal = new QueueFileJournal();
        $journal->setEntity($queue);
        $journal->setStorage(__DIR__ . '/../logs/queue.log');

        $logger = new Logger($journal);

        $worker = new QueueWorker($queue, $logger);
        $worker->run($input->getOption('delay'), $input->getOption('memory'), $input->getOption('sleep'), $input->getOption('tries'));
    }
}