<?php

namespace eazy\http\components;

use eazy\Eazy;
use eazy\http\App;
use eazy\http\Component;
use eazy\http\ContextComponent;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\ViewNotFoundException;

/**
 * @property string $layout
 */
class View extends Component
{
    public $defaultLayout = '@app/views/layouts/main';

    public string $defaultLayoutPath = '';
    public string $defaultViewPath = '';

    /**
     * Default render file extension.
     * @var string
     */
    public $defaultExtension = 'php';

    /**
     * Render html title.
     * @var
     */
    public $title;


    public $assetBundles = [];

    public function init()
    {
        $this->defaultLayoutPath = $this->defaultLayoutPath ?: APP_PATH . '/views/layouts';
        $this->defaultViewPath = $this->defaultViewPath ?: APP_PATH . '/views';
        parent::init(); // TODO: Change the autogenerated stub
    }


    public function render($view, $params = [])
    {
        $viewFile = $this->findViewFile($view);
        $content = $this->renderFile($viewFile, $params);
        return $this->renderContent($content);
    }


    /**
     * @param $content
     *
     * @return bool|string
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile();
        if ($layoutFile !== false) {
            return $this->renderFile($layoutFile, ['content' => $content], $this);
        }
        return $content;
    }

    public function getLayout()
    {
        return $this->context->layout ?? $this->defaultLayout;
    }

    public function setLayout(string $path)
    {
        $this->context->layout = $path;
        return $this;
    }


    /**
     *
     *
     * @param $view View
     *
     * @return bool|string
     */
    public function findLayoutFile()
    {
        if (strncmp($this->layout, '@', 1) === 0) {
            $file = App::getAlias($this->layout);
        }elseif (strncmp($this->layout, '/', 1) === 0) {
            $file = $this->defaultLayoutPath . DIRECTORY_SEPARATOR . substr($this->layout, 1);
        } else {
            $file = $this->defaultLayoutPath . DIRECTORY_SEPARATOR . $this->layout;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.php';
        return $path;
    }

    
    public function findViewFile($view)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = App::getAlias($view);
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            $file = $this->defaultViewPath . DIRECTORY_SEPARATOR . substr($view, 1);
        } else {
            $file = $this->defaultViewPath . DIRECTORY_SEPARATOR . $view;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.php';

        return $path;
    }

   
    public function renderFile($viewFile, $params, $context = null)
    {
        $viewFile = $requestedFile = App::getAlias($viewFile);
        var_dump($viewFile);
        if (!is_file($viewFile)) {
            throw new ViewNotFoundException("The view file does not exist: $viewFile");
        }

        //        $oldContext = $this->context;
        //        if ($context !== null) {
        //            $this->context = $context;
        //        }
        $output = '';
        //        $this->_viewFiles[] = [
        //            'resolved' => $viewFile,
        //            'requested' => $requestedFile
        //        ];
        $output = $this->renderPhpFile($viewFile, $params);
        //        array_pop($this->_viewFiles);
        //        $this->context = $oldContext;

        return $output;
    }

   
    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        }catch (Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }


    public function registerAssetBundle($name)
    {
        if (!isset($this->assetBundles[$name])) {
            $am = $this->getAssetManager();
            $bundle = $am->getBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
            $this->assetBundles[$name] = $bundle;
        } elseif ($this->assetBundles[$name] === false) {
            throw new InvalidConfigException("A circular dependency is detected for bundle '$name'.");
        } else {
            $bundle = $this->assetBundles[$name];
        }

        if ($position !== null) {
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            if ($pos === null) {
                $bundle->jsOptions['position'] = $pos = $position;
            } elseif ($pos > $position) {
                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
            }
            // update position for all dependencies
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
        }

        return $bundle;
    }


}