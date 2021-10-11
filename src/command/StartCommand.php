<?php

namespace eazy\http\command;

use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use eazy\http\event\StartCallback;
use eazy\http\event\SwooleEvent;
use eazy\http\Server;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected string $name = 'http:start';

    /**
     * {@inheritdoc}
     */
    protected string $description = 'Start eazy http server.';

    protected array $arguments = [
    ];

    /**
     * {@inheritdoc}
     */
    protected array $options = [
        ['daemonize', 'd', InputOption::VALUE_OPTIONAL, 'Default `true`'],
        ['server', 's', InputOption::VALUE_REQUIRED, 'Which server want to start?'],
        ['mode', 'm', InputOption::VALUE_OPTIONAL, 'DEV OR PROD']
    ];

    private array $defaultSetting = [];

    /**
     * {@inheritdoc}
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $input->getOption('server');
//        $isDaemonize = strtolower($input->getOption('daemonize')) == 'true' ? true : false;
        $config = require APP_CONFIG;
        $serverConfigs = ($config[Bootstrap::$packageName]['server']);
        if ($server) {
            $server = explode(',', $server);
        }
        var_dump($server);
        foreach ($serverConfigs as $serverConfig) {
            // daemonize mode
            $serverConfig['setting']['daemonize'] = true;
            if (is_array($server) && isset($serverConfig['name']) && in_array($serverConfig['name'], $server)) {
                (new Server($serverConfig))->run();
            }elseif($server === NULL){
                (new Server($serverConfig))->run();
            }
        }
//        var_dump($server);

//        $isDaemonize = $input->getOption('daemonize');
//        $config = $input->getOption('config');


//        $config = require APP_CONFIG;
//        $config = ($config[Bootstrap::$packageName]['server']);
//        var_dump(count($config));
//        if (count($config) > 1) {
//
//        }
//        $config = ($config[Bootstrap::$packageName]['server']);
//        var_dump($aa);
//         $output->write("fuck you~");
         return 0;
    }


    private function initSetting()
    {
        $this->defaultSetting = [
            'enable_static_handler' => true,
            'document_root' => $this->installPath . '/web',
            'worker_num' => 2,
            'enable_coroutine' => true,
            // SWOOLE_HOOK_ALL
            'hook_flags' => SWOOLE_HOOK_ALL,
            'daemonize'  => false,
            'log_file'   => $this->installPath . '/runtime/http.log',
            'pid_file'   => $this->installPath . '/runtime/server.pid',
        ];
    }
}