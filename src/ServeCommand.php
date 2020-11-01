<?php

namespace abenevaut\BedrockConsole;

use abenevaut\BedrockConsole\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

class ServeCommand extends Command
{

    /**
     * @var ?InputInterface
     */
    protected $input = null;

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
            ->setDescription('Serve the application on the PHP development server')
            ->addArgument('host', InputArgument::OPTIONAL, 'host, default: localhost')
            ->addArgument('port', InputArgument::OPTIONAL, 'port, default : 8000')
            ->addOption(
                'install',
                'i',
                InputArgument::OPTIONAL,
                'Install server.php file at project root (required one time after installation)'
            );
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
        if ($input->getOption('install')) {
            return !copy(__DIR__ . '/../server.php', ABSPATH . '../../server.php');
        }

        if (!is_file(ABSPATH . '../../server.php')) {
            throw new \Exception('You have to run `serve --install` before using the serve command!');
        }

        $this->input = $input;
        chdir(ABSPATH . '..');
        $output->writeln("<info>Bedrock development server started:</info> <http://{$this->host()}:{$this->port()}>");
        passthru($this->serverCommand(), $status);

        if ($status && $this->canTryAnotherPort()) {
            $this->portOffset += 1;

            return $this->execute($input, $output);
        }

        return $status;
    }

    /**
     * Get the full server command.
     *
     * @return string
     */
    protected function serverCommand()
    {
        return sprintf(
            '%s -S %s:%s %s',
            (new PhpExecutableFinder())->find(false),
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
        return $this->input->getArgument('host') ?? 'localhost';
    }

    /**
     * Get the port for the command.
     *
     * @return string
     */
    protected function port()
    {
        return ($this->input->getArgument('port') ?? 8000) + $this->portOffset;
    }

    /**
     * Check if command has reached its max amount of port tries.
     *
     * @return bool
     */
    protected function canTryAnotherPort()
    {
        return is_null($this->input->getArgument('port')) && (9 > $this->portOffset);
    }
}
