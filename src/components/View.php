<?php

namespace eazy\http\components;

use eazy\Eazy;
use eazy\http\App;
use eazy\http\Component;
use eazy\http\ContextComponent;
use eazy\http\exceptions\ViewNotFoundException;

/**
 * @property string $layout
 */
class View extends ContextComponent
{
    public $defaultLayout = '@app/views/layouts/main';

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
        var_dump('*******************');
        var_dump($this->attributes['layout'] ?? $this->defaultLayout);
        return $this->attributes['layout'] ?? $this->defaultLayout;
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
        if (is_string($this->layout)) {
            $layout = $this->layout;
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = App::getAlias($layout);
        }
        //        elseif (strncmp($layout, '/', 1) === 0) {
        //            $file = YiiS::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
        //        } else {
        //            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        //        }

        var_dump('//////////////////////');
        var_dump($file);
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
            //            if (YiiS::$app->controller !== null) {
            //                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            //            } else {
            //                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
            //            }
        } else {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

   
    public function renderFile($viewFile, $params, $context = null)
    {
        $viewFile = $requestedFile = App::getAlias($viewFile);
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
}