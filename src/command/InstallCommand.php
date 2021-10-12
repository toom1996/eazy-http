<?php

namespace eazy\http\command;

use eazy\helpers\BaseArrayHelper;
use eazy\base\BaseCommand;
use eazy\Eazy;
use eazy\helpers\BaseFileHelper;
use eazy\http\Bootstrap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends BaseCommand
{
    /**
     * {@inheritdoc }
     */
    protected string $name = 'http:install';
    
    /**
     * {@inheritdoc }
     */
    protected string $description = 'Generate base code for run http server when you run first.';
    
    /**
     * {@inheritdoc }
     */
    protected array $options = [
        ['path', 'p', InputOption::VALUE_OPTIONAL, 'Specify install directory.'],
    ];

    /**
     * Install file path.
     * @var string 
     */
    private string $installPath;

    /**
     * Check swoole extension and install initialize code.
     *
     * It can generate a fast-running basic template code.
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!extension_loaded('swoole')) {
            $output->writeln("<error>The Swoole PHP extension is required by Eazy. Please see: https://wiki.swoole.com/#/environment to install swoole extension.</error>");
            return 0;
        }
        
        $helper = $this->getHelper('question');
        $question = new Question("<comment>Does this command overwrite the modified code, or execute it? (y/n)</comment>  ", 'y');
        $answer = $helper->ask($input, $output, $question);
        if ($answer === 'y') {
            $this->installPath = $input->getOption('path') ? APP_PATH . '/' . trim($input->getOption('path'),'/') : APP_PATH;
            $this->install();
        }

        $output->writeln("<info>install successful!</info>");
        return 0;
    }

    /**
     * Generate basic code for http server.
     *
     * Contains file list:
     *  - a controller file as `/controllers/SiteController.php`.
     *  - a view file as `/views/index.php`.
     *  - a layout file as `/views/layout/main.php`.
     *  - a default server config to `app.php`.
     */
    private function install()
    {
        // generate config.
        $this->generateConfig();
        // generate default controller.
        $this->generateController();
        // generate default view.
        $this->generateView();
        // generate runtime directory.
        $this->generateRuntimeDirectory();
    }

    /**
     * Returns default config content.
     * @return array[]
     */
    private function getServerConfig()
    {
        return [
            // http server config.
            'server' => [
                [
                    'name' => 's1',
                    'host' => "0.0.0.0",
                    'port' => 9502,
                    'setting' => [
                        'enable_static_handler' => true,
                        'document_root' => $this->installPath . '/web',
                        'worker_num' => swoole_cpu_num(),
                        'enable_coroutine' => true,
                        // SWOOLE_HOOK_ALL
                        'hook_flags' => SWOOLE_HOOK_ALL,
                        'daemonize'  => false,
                        'log_file'   => $this->installPath . '/runtime/s1.log',
                        'pid_file'   => $this->installPath . '/runtime/s1.pid',
                    ],
                ],
                [
                    'name' => 's2',
                    'host' => "0.0.0.0",
                    'port' => 9503,
                    'setting' => [
                        'enable_static_handler' => true,
                        'document_root' => $this->installPath . '/web',
                        'worker_num' => swoole_cpu_num(),
                        'enable_coroutine' => true,
                        // SWOOLE_HOOK_ALL
                        'hook_flags' => SWOOLE_HOOK_ALL,
                        'daemonize'  => false,
                        'log_file'   => $this->installPath . '/runtime/s2.log',
                        'pid_file'   => $this->installPath . '/runtime/s2.pid',
                    ],
                ]
            ],
            'config' => [
                'aliases' => [
                    '@controllers' => $this->installPath . '/controllers',
                ],
                'bootstrap' => [],
                'components' => [],
            ]
        ];
    }

    /**
     * Generate config code.
     */
    private function generateConfig()
    {
        $config = require APP_CONFIG;
        $config[Bootstrap::$packageName] = $this->getServerConfig();
        $config = BaseArrayHelper::varexport($config, true);
        $this->saveFile(APP_CONFIG, "<?php\n\nreturn $config;\n");
    }

    /**
     * Genetate controller file.
     */
    private function generateController()
    {
        $file = $this->installPath . '/controllers/SiteController.php';
        $this->saveFile($file, "<?php

namespace app\controllers;

class SiteController
{
    public function actionIndex()
    {
        return " . '$this->render' . "('@eazy/views/index');
    }
}");
    }

    /**
     * Generate view.
     */
    private function generateView()
    {
        $this->generateLayout();
        $viewFile = $this->installPath . '/views/index.php';
        
        $this->saveFile($viewFile, "<?php\necho'hello eazy!';");
    }

    /**
     * Generate layout.
     */
    private function generateLayout()
    {
        $layoutFile = $this->installPath . '/views/layouts/main.php';
        
        $this->saveFile($layoutFile, '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
    <meta name=viewport content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title><?= $this->title ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
<?= $content ?>
</body>
</html>
'
);
    }

    private function generateRuntimeDirectory()
    {
        BaseFileHelper::createDirectory($this->installPath . '/runtime');
    }

    /**
     * Save content to file.
     * @param  string  $file file path.
     * @param string $content save content.
     */
    private function saveFile(string $file, string $content)
    {
        $pathInfo = pathinfo($file);
        BaseFileHelper::createDirectory($pathInfo['dirname']);
        file_put_contents($pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'], $content);
        // invalidate opcache of extensions.php if exists
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($file, true);
        }
    }
}