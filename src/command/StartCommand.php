<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use Swoole\FastCGI;
use Swoole\Process;
use Swoole\Process as SwooleProcess;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected string $name = 'server:start';

    /**
     * {@inheritdoc}
     */
    protected string $description = 'Start server. usage: `--server=[server]` or `-s [server]`';

    /**
     * {@inheritdoc}
     */
    protected array $options = [
        ['server', 's', InputOption::VALUE_REQUIRED, 'Which server want to start ?'],
        ['daemonize', 'd', InputOption::VALUE_NONE, 'Run with daemonize ?'],
    ];


    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $inputServer = $input->getOption('server');
        if ($inputServer === null) {
            $output->writeln("<comment>Please select at least one server.</comment>");
            $this->renderServerTable($output);
            return 0;
        }

        $inputServer = explode(',', $inputServer);

        foreach ($inputServer as $name) {
            $config = \server("servers.{$name}");
            if (!$config) {
                $output->writeln("<error>Server#{$name} not exist.</error>");
                continue;
            }
            $this->startServer($config, $name);
            $output->writeln("<info>Server#{$name} start.</info>");
        }

        return 0;
    }

    /**
     * Render server table.
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    protected function renderServerTable(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name', 'host:port', 'notes', 'status']);
        $servers = \server('servers');
        foreach ($servers as $name => $server) {
            $table->addRows([
                [$name, "{$server['host']}:{$server['port']}", $server['note'] ?? '', $this->getServerRunStatus($name)]
            ]);
        }
        $table->render();
    }

    protected function getServerRunStatus($name)
    {
        $pidFile = server("servers.{$name}.settings.pid_file");
        $serverPid = 0;
        if (\server("servers.{$name}.settings.pid_file")) {
            $serverPid = file_get_contents($pidFile);
        }else{
            $pidFile = config('aliases.@runtime') . "/{$name}.pid";
            if (file_exists(config('aliases.@runtime') . "/{$name}.pid")) {
                $serverPid = file_get_contents($pidFile);
            }
        }

        return !Process::kill($serverPid, 0) ? '<comment>stop</comment>' : "<info>running</info> ($serverPid)";
    }

    private function startServer($config, $name)
    {
        $process = new SwooleProcess(function (\Swoole\Process $childProcess) use ($config, $name) {
            $type = $config['type'];
            $server = new $type($config);
            $server->run($name);
        });
        $process->start();
        Process::wait();
    }
}