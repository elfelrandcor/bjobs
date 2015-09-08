<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BJobsCommand extends Command {

    protected function configure() {
        $this
            ->setName('bjobs:process')
            ->setDescription('Get work done!')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Start process...');
    }
}