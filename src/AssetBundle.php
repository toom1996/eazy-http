<?php

namespace eazy\http;

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
            $this->sourcePath = rtrim(App::getAlias($this->sourcePath), '/\\');
        }
        if ($this->basePath !== null) {
            $this->basePath = rtrim(App::getAlias($this->basePath), '/\\');
        }
        if ($this->baseUrl !== null) {
            $this->baseUrl = rtrim(App::getAlias($this->baseUrl), '/');
        }
    }

    public static function register()
    {
        App::$component->view->registerAssetBundle(get_called_class());
    }

    public function publish($bundle)
    {
        if ($this->sourcePath !== null && !isset($this->basePath, $this->baseUrl)) {
            list($this->basePath, $this->baseUrl) = $bundle->publish($this->sourcePath, $this->publishOptions);
        }

        if (isset($this->basePath, $this->baseUrl) && ($converter = $am->getConverter()) !== null) {
            foreach ($this->js as $i => $js) {
                if (is_array($js)) {
                    $file = array_shift($js);
                    if (Url::isRelative($file)) {
                        $js = ArrayHelper::merge($this->jsOptions, $js);
                        array_unshift($js, $converter->convert($file, $this->basePath));
                        $this->js[$i] = $js;
                    }
                } elseif (Url::isRelative($js)) {
                    $this->js[$i] = $converter->convert($js, $this->basePath);
                }
            }
            foreach ($this->css as $i => $css) {
                if (is_array($css)) {
                    $file = array_shift($css);
                    if (Url::isRelative($file)) {
                        $css = ArrayHelper::merge($this->cssOptions, $css);
                        array_unshift($css, $converter->convert($file, $this->basePath));
                        $this->css[$i] = $css;
                    }
                } elseif (Url::isRelative($css)) {
                    $this->css[$i] = $converter->convert($css, $this->basePath);
                }
            }
        }
    }
}