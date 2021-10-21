<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Swoole\FastCGI;
use Swoole\Process;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Swoole\Process as SwooleProcess;
use function Co\run;

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
        foreach ($serverConfigs as $serverConfig) {
            $serverConfig['setting']['daemonize'] = $input->getOption('daemonize');
            if (is_array($server) && isset($serverConfig['name']) && in_array($serverConfig['name'], $server)) {
                $process = new SwooleProcess(function (\Swoole\Process $childProcess) use ($serverConfig) {
                    $server = new Server($serverConfig);
                    $server->run();
                });
                $process->start(); // 启动子进程
                $output->writeln("<info>Server#{$serverConfig['name']} start.</info>");
                Process::wait();
            }
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
        $table->setHeaders(['name', 'port', 'notes', 'status']);
        $servers = \server('servers');
        foreach ($servers as $name => $server) {
            $this->getServerRunStatus($name);
            $table->addRows([
                [$name, $server['port'], $server['note'] ?? '', '<comment>stop</comment>']
            ]);
        }
        $table->render();
    }

    protected function getServerRunStatus($name)
    {
        $servers = @file_get_contents(\server("servers.{$name}.settings.pid_file") ?? '/dev/null');
        if (!$servers) {
            $process = new Process(function(\Swoole\Process $worker) {
//                exec('ps -ef | grep entrypoint |grep -v grep | awk \'{print $2}\'', $return);
                $worker->exec('/bin/sh', array('-c', 'ps -ef | grep entrypoint |grep -v grep | awk \'{print $2}\''));
            }, true, 1, true);
            $process->start();
            Process::wait();

            run(function() use($process) {
                $socket = $process->exportSocket();
                var_dump($socket->recv());
                echo "from exec: " . $socket->recv() . "\n";
            });
        }
    }
}