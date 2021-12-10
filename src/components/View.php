<?php

namespace eazy\http\components;

use eazy\http\App;
use eazy\http\AssetBundle;
use eazy\http\Component;
use eazy\http\ContextComponent;
use eazy\http\Eazy;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\exceptions\ViewNotFoundException;

/**
 * @property string $layout
 */
class View extends ContextComponent
{
    /**
     * Default layout file path.
     * @var string
     */
    public $defaultLayout = '@app/views/layouts/main';

    /**
     * Default laytous file directory path.
     * @var string
     */
    public string $defaultLayoutPath = '';

    /**
     * Default view file directory path.
     * @var string
     */
    public string $defaultViewPath = '';

    public $assetBundles = [];

    /**
     * Defined default path.
     */
    public function init()
    {
        $this->defaultLayoutPath = $this->defaultLayoutPath ?: APP_PATH . '/views/layouts';
        $this->defaultViewPath = $this->defaultViewPath ?: APP_PATH . '/views';
    }

    /**
     * Render php file.
     * 
     * -  Support
     *      - render alias path or file path.
     *      - made available params in the view.
     *
     * ```php
     * // render based on alias.
     * Eazy::$component->view->render('@app/views/index');
     * 
     * // render based on file path.
     * Eazy::$component->view->render('index');
     * 
     * // render with params.
     * Eazy::$component->view->render('index', ['foo' => 'bar']);
     * ```
     * @param string $view Render file path.
     * @param  array  $params the parameters (name-value pairs) that should be made available in the view.
     *
     * @return bool|string
     * @throws \eazy\http\exceptions\ViewNotFoundException
     */
    public function render(string $view, array $params = [])
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
            return $this->renderFile($layoutFile, ['content' => $content, 'title' => $this->title], $this);
        }
        return $content;
    }

    public function getTitle()
    {
        return $this->properties['title'] ?: '';
    }

    public function setTitle(string $title)
    {
        $this->setProperty('title', $title);
        return $this;
    }

    public function getLayout()
    {
        return $this->properties['layout'] ?? $this->defaultLayout;
    }

    public function setLayout(string $path)
    {
        $this->setProperty('layout', $path);
        return $this;
    }


    /**
     * @param $view View
     *
     * @return bool|string
     */
    public function findLayoutFile()
    {
        if (strncmp($this->layout, '@', 1) === 0) {
            $file = Eazy::getAlias($this->layout);
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

    /**
     * @param $view
     *
     * @return bool|string
     */
    public function findViewFile($view)
    {
        if (strncmp($view, '@', 1) === 0) { // e.g. "@app/views/main"
            $file = Eazy::getAlias($view);
        } elseif (strncmp($view, '/', 1) === 0) { // e.g. "/site/index"
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
        $viewFile = $requestedFile = Eazy::getAlias($viewFile);
//        var_dump($viewFile);
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
//            $am = $this->getAssetManager();
            $bundle = $this->loadBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            $this->assetBundles[$name] = $bundle;
        }
//
//        if ($position !== null) {
//            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
//            if ($pos === null) {
//                $bundle->jsOptions['position'] = $pos = $position;
//            } elseif ($pos > $position) {
//                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
//            }
//            // update position for all dependencies
//            foreach ($bundle->depends as $dep) {
//                $this->registerAssetBundle($dep, $pos);
//            }
//        }
//
        return $this->assetBundles[$name];;
    }

    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);

//        $this->trigger(self::EVENT_BEGIN_PAGE);
    }

    public function endPage()
    {
//        $this->trigger(self::EVENT_END_PAGE);
        ob_end_flush();
    }

    public function getBundle($name, $publish = true)
    {
//        if (!isset($this->bundles[$name])) {
//            return $this->bundles[$name] = $this->loadBundle($name, [], $publish);
//        }
//        else
        if ($this->bundles[$name] instanceof AssetBundle) {
            return $this->bundles[$name];
        } elseif (is_array($this->bundles[$name])) {
//            return $this->bundles[$name] = $this->loadBundle($name, $this->bundles[$name], $publish);
//        } elseif ($this->bundles[$name] === false) {
//            return $this->loadDummyBundle($name);
        }

        throw new InvalidConfigException("Invalid asset bundle configuration: $name");
    }

    protected function loadBundle($name)
    {
        $bundle = Eazy::createObject($name);
//        if ($publish) {
//            $bundle->publish($this);
//        }

        return $bundle;
    }

}