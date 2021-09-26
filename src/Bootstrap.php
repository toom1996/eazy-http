<?php

namespace eazy\http;

use eazy\base\BootstrapInterface;
use Symfony\Component\Console\Command\Command;

class Bootstrap extends Command implements BootstrapInterface
{

    protected function configure()
    {
        $this->setName('hello-world')
            ->setDescription('Prints Hello-World!')
            ->setHelp('Demonstration of custom commands created by Symfony Console component.')
            ->addArgument('username', InputArgument::REQUIRED, 'Pass the username.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Hello World!, %s', $input->getArgument('username')));
    }

    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }
}