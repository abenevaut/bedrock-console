<?php

namespace abenevaut\BedrockConsole;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{

    /**
     * @var array
     */
    protected $container;

    /**
     * Command constructor.
     *
     * @param array $container
     * @param null|string $name
     */
    public function __construct(array $container, ?string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
    }
}
