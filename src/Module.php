<?php

namespace eazy\http;

use eazy\Eazy;
use eazy\http\components\Request;
use eazy\http\exceptions\InvalidConfigException;

class Module extends ServiceLocator
{
    public function render($view, $params = [])
    {
        $content = $this->view->render($view, $params);
        return $this->renderContent($content);
    }

    /**
     * @param $content
     *
     * @return bool|string
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile($this->view);
        if ($layoutFile !== false) {
            return App::$get->getView()->renderFile($layoutFile, ['content' => $content], $this);
        }

        return $content;
    }

    /**
     *
     *
     * @param $view View
     *
     * @return bool|string
     */
    public function findLayoutFile($view)
    {
        if (is_string($this->layout)) {
            $layout = $this->layout;
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = Eazy::getAlias($layout);
        }
        //        elseif (strncmp($layout, '/', 1) === 0) {
        //            $file = YiiS::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
        //        } else {
        //            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        //        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }
}