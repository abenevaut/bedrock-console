<?php

namespace abenevaut\BedrockConsole;

use abenevaut\BedrockConsole\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

class ServeCommand extends Command
{

    /**
     * The current port offset.
     *
     * @var int
     */
    protected $portOffset = 0;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription('Serve the application on the PHP development server');
    }

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        chdir(ABSPATH . '..');

        $output->writeln("<info>Bedrock development server started:</info> <http://{$this->host()}:{$this->port()}>");

        passthru($this->serverCommand(), $status);

//        if ($status && $this->canTryAnotherPort()) {
//            $this->portOffset += 1;
//
//            return $this->handle();
//        }

        return $status;
    }

    /**
     * Get the full server command.
     *
     * @return string
     */
    protected function serverCommand()
    {
        return sprintf('%s -S %s:%s %s',
            (new PhpExecutableFinder)->find(false),
            $this->host(),
            $this->port(),
            ABSPATH . '../../server.php'
        );
    }

    /**
     * Get the host for the command.
     *
     * @return string
     */
    protected function host()
    {
        return 'localhost';
    }

    /**
     * Get the port for the command.
     *
     * @return string
     */
    protected function port()
    {
        $port = 8000;

        return $port + $this->portOffset;
    }

    /**
     * Check if command has reached its max amount of port tries.
     *
     * @return bool
     */
    protected function canTryAnotherPort()
    {
        return is_null($this->input->getOption('port')) &&
               ($this->input->getOption('tries') > $this->portOffset);
    }
}
