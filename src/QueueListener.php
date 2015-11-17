<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */


namespace JuriyPanasevich\BJobs;

use Symfony\Component\Process\Process;

class QueueListener {

    const PHP_BINARY = 'php';
    const APP_FILE = 'app.php';

    public $workerCommand;
    public $sleep;
    public $maxTries;
    public $commandPath;
    /** @var  callable */
    protected $outputHandler;
    private $queue;

    public function __construct() {
        $this->workerCommand = sprintf(
            '%s %s work --queue="%%s" --delay=%%s --memory=%%s --sleep=%%s --tries=%%s',
            self::PHP_BINARY,
            self::APP_FILE
            );
        $this->setOutputHandler(function($type, $line) {
            if ($type == Process::ERR) {
                printf('%s', $line);
            }
            printf('%s', $line);
        });
    }

    /**
     * Listen to the given queue connection.
     *
     * @param  string $queue
     * @param int $delay
     * @param int $memory
     * @param int $timeout
     * @param int $sleep
     * @param int $tries
     */
    public function listen($queue, $delay = 0, $memory = 256, $timeout = 0, $sleep = 0, $tries = 0) {
        $this->sleep = $sleep;
        $this->maxTries = $tries;
        $this->queue = $queue;

        $process = $this->makeProcess($queue, $delay, $memory, $timeout);
        while(true) {
            try {
                $this->runProcess($process, $memory);
            } catch (\Exception $e) {
                $this->stop();
            }
            //@todo Handle stop signal
        }
    }

    /**
     * Run the given process.
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  int  $memory
     * @return void
     */
    public function runProcess(Process $process, $memory) {
        $process->run(function($type, $line) {
            $this->handleWorkerOutput($type, $line);
        });

        if ($this->memoryExceeded($memory)) {
            $this->stop();
        }
    }

    /**
     * Create a new Symfony process for the worker.
     *
     * @param  string  $queue
     * @param  int     $delay
     * @param  int     $memory
     * @param  int     $timeout
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($queue, $delay, $memory, $timeout) {
        $string = $this->workerCommand;
        $command = sprintf(
            $string, $queue, $delay,
            $memory, $this->sleep, $this->maxTries
        );
        return new Process($command, $this->commandPath, null, null, $timeout);
    }

    /**
     * Handle output from the worker process.
     *
     * @param  int  $type
     * @param  string  $line
     * @return void
     */
    protected function handleWorkerOutput($type, $line) {
        if (isset($this->outputHandler)) {
            call_user_func($this->outputHandler, $type, $line);
        }
    }
    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int   $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit) {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }
    /**
     * Stop listening and bail out of the script.
     *
     * @return void
     */
    public function stop() {
        die;
    }

    /**
     * @param callable $outputHandler
     */
    public function setOutputHandler($outputHandler) {
        $this->outputHandler = $outputHandler;
    }
}