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
                $this->css[$i] = $this->convert($css);
                if (UrlHelper::isRelative($css)) {
                    $this->css[$i] = $this->convert($css);
                }
            }
        }
    }

    public function convert($asset)
    {
        if (UrlHelper::isRelative($asset)) {
            $asset = $asset = '/' . trim($asset, '/');
        }

        return $asset;
    }
    
}