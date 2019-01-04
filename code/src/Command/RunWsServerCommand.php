<?php

namespace App\Command;

use App\Services\RatchetServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunWsServerCommand extends Command
{
    protected static $defaultName = 'app:run-ws-server';

    private $server;

    public function __construct(RatchetServer $server)
    {
        parent::__construct(null);
        $this->server = $server;
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts WS server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo "Starting server\n";
        $this->server->start(1337);
    }
}
