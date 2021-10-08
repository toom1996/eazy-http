<?php

namespace eazy\http\command;

use eazy\base\BaseArrayHelper;
use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\http\Bootstrap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends BaseCommand
{
    protected string $name = 'http:install';

    protected string $description = 'Install eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $argument = [];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->install();
        $output->writeln(('Hello World!'));

        return 0;
    }

    private function install()
    {
        $config = require APP_CONFIG;
        if (!isset($config[Bootstrap::$packageName])) {
            $config[Bootstrap::$packageName] = [
                'server' => [
                    'host' => "0.0.0.0",
                    'port' => 9503,
                    'event' => [
                        \eazy\http\event\SwooleEvent::SWOOLE_ON_START => [\eazy\http\event\StartCallback::class, 'onStart'],
                    ],
                    'setting' => [
                        //        'enable_static_handler' => APP_DEBUG,
                        //        'document_root' => APP_PATH . '/web',
                        'worker_num' => 2,
                        'enable_coroutine' => true,
                        //                        'hook_flags' => SWOOLE_HOOK_ALL,
                        'daemonize' => false,
                        'log_file' => APP_PATH . '/runtime/http.log',
                        'pid_file' => APP_PATH . '/runtime/server.pid',
                    ],
                ],
                'config' => []
            ];
            $config =  BaseArrayHelper::varexport($config, true);;
            file_put_contents(APP_CONFIG, "<?php\n\nreturn $config;\n");
            // invalidate opcache of extensions.php if exists
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($file, true);
            }
        }
    }
}