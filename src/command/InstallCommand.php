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
use Symfony\Component\Console\Question\Question;

class InstallCommand extends BaseCommand
{

    protected string $name = 'http:install';

    protected string $description = 'Install eazy http server.';

    protected string $help = 'This command allow you to create models...';

    protected array $argument = [];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question =
            new Question("Does this command overwrite the modified code, or execute it? (y/n)  ",
                'y');
        $answer = $helper->ask($input, $output, $question);
        if ($answer === 'y') {
            $this->install($output);
        }

        return 0;
    }

    private function install(OutputInterface $output)
    {
        $config = require APP_CONFIG;
        $config[Bootstrap::$packageName] = $this->getServerConfig();
        $config = BaseArrayHelper::varexport($config, true);;
        file_put_contents(APP_CONFIG, "<?php\n\nreturn $config;\n");
        // invalidate opcache of extensions.php if exists
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($file, true);
        }
        $output->writeln("ok");
    }


    private function getServerConfig()
    {
        return [
            'server' => [
                'host' => "0.0.0.0",
                'port' => 9503,
                'setting' => [
                    'enable_static_handler' => true,
                    'document_root' => APP_PATH . '/web',
                    'worker_num' => 2,
                    'enable_coroutine' => true,
                    // SWOOLE_HOOK_ALL
                    'hook_flags' => SWOOLE_HOOK_ALL,
                    'daemonize'  => false,
                    'log_file'   => APP_PATH.'/runtime/http.log',
                    'pid_file'   => APP_PATH.'/runtime/server.pid',
                ],
            ],
            'config' => [
                'aliases' => [
                    '@controllers' => APP_PATH . '/controllers',
                ],
                'bootstrap' => [],
                'components' => [],
            ]
        ];
    }
}