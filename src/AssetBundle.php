<?php

namespace eazy\http;

use eazy\http\helpers\UrlHelper;

class AssetBundle extends BaseObject
{
    public $sourcePath;

    public $basePath;

    public $baseUrl;

    public $depends = [];
    public $js = [];
    public $css = [];
    public $jsOptions = [];
    public $cssOptions = [];
    public $publishOptions = [];

    public function init()
    {
        if ($this->sourcePath !== null) {
            $this->sourcePath = rtrim(Eazy::getAlias($this->sourcePath), '/\\');
        }
        if ($this->basePath !== null) {
            $this->basePath = rtrim(Eazy::getAlias($this->basePath), '/\\');
        }
        if ($this->baseUrl !== null) {
            $this->baseUrl = rtrim(Eazy::getAlias($this->baseUrl), '/');
        }

        $this->publish();
    }

    /**
     * @param $view \eazy\http\components\View
     */
    public static function register($view)
    {
        $view->registerAssetBundle(get_called_class());
    }

    public function publish()
    {
        echo __FUNCTION__;

//        if ($this->sourcePath !== null && !isset($this->basePath, $this->baseUrl)) {
//            [$this->basePath, $this->baseUrl] = $bundle->publish($this->sourcePath, $this->publishOptions);
//        }
//
        if (isset($this->basePath, $this->baseUrl)) {
            foreach ($this->js as $i => $js) {
                if (UrlHelper::isRelative($js)) {
                    $this->js[$i] = $this->convert($js);
                }
            }
            foreach ($this->css as $i => $css) {
                if (UrlHelper::isRelative($css)) {
                    $this->css[$i] = $this->convert($css);
                }
            }
        }
        var_dump($this->css);
    }

    public function convert($asset)
    {
        $asset = $this->basePath . $asset;
        $pos = strrpos($asset, '.');
        var_dump(Eazy::$component->request->getRemoteAddr());
        if ($pos !== false) {
//            $ext = substr($asset, $pos + 1);
//            if (isset($this->commands[$ext])) {
//                list($ext, $command) = $this->commands[$ext];
//                $result = substr($asset, 0, $pos + 1) . $ext;
//                if ($this->forceConvert || @filemtime("$basePath/$result") < @filemtime("$basePath/$asset")) {
//                    $this->runCommand($command, $basePath, $asset, $result);
//                }
//
//                return $result;
//            }
        }

        return $asset;
    }
}