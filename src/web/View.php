<?php

namespace eazy\http\web;

use eazy\http\AssetBundle;
use eazy\http\Eazy;
use eazy\http\helpers\ArrayHelper;
use eazy\http\helpers\HtmlHelper;

/**
 * @property array $jsFiles
 * @property array $cssFiles
 * @property array $assetBundles
 */
class View extends \eazy\http\components\View
{
    const PH_HEAD = '<![CDATA[BLOCK-HEAD]]>';

    public function head()
    {
        return self::PH_HEAD;
    }

    public function endPage()
    {
        //        $this->trigger(self::EVENT_END_PAGE);
        $content = ob_get_clean();

        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
//            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
//            self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);
    }

    protected function renderHeadHtml()
    {
        $lines = [];
        if ($this->cssFiles) {
            $lines[] = implode("\n", $this->cssFiles);
        }
//
//        if (!empty($this->linkTags)) {
//            $lines[] = implode("\n", $this->linkTags);
//        }
//        if (!empty($this->cssFiles)) {
//            $lines[] = implode("\n", $this->cssFiles);
//        }
//        if (!empty($this->css)) {
//            $lines[] = implode("\n", $this->css);
//        }
//        if (!empty($this->jsFiles[self::POS_HEAD])) {
//            $lines[] = implode("\n", $this->jsFiles[self::POS_HEAD]);
//        }
//        if (!empty($this->js[self::POS_HEAD])) {
//            $lines[] = Html::script(implode("\n", $this->js[self::POS_HEAD]));
//        }
        return empty($lines) ? '' : implode("\n", $lines);
    }

    public function registerJs()
    {

    }

    public static function registerCss($file)
    {
        echo __FUNCTION__;
        $css = Eazy::$component->view->cssFiles;
        $css[] = self::registerFile('css', $file);
        Eazy::$component->view->cssFiles = $css;
    }


    public static function registerFile($type, $file)
    {
        if ($type == 'css') {
            $files = HtmlHelper::cssFile($file);
        }

        return $files;
    }

    public function registerAssetBundle($name, $bundleName = null)
    {
        if (isset($this->assetBundles[$name])) {
            return $this->assetBundles[$name];
        }

        $bundle = $this->loadBundle($name);

        // 处理依赖关系
//        foreach ($bundle->depends as $depend) {
//            $this->registerAssetBundle($depend, $name);
//        }
//        $this->assetBundles[$name] = false;
        // register dependencies
//        $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
        $this->registerBundleCssFiles($bundle);
//        $this->registerBundleFiles('js', $name,  $bundle->js);
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
        $this->loadBundleFiles($name);
    }

    public function registerBundleCssFiles(AssetBundle $bundle)
    {
        foreach ($bundle->css as $file) {
            $assetBundle[$bundle::class]['css'][] = self::registerFile('css', $file);
        }

        if (isset($assetBundle)) {
            $this->assetBundles = ArrayHelper::merge($this->assetBundles, $assetBundle);
        }
    }

    protected function loadBundle($name)
    {
        $bundle = Eazy::createObject($name);
        //        if ($publish) {
//        $bundle->publish();
        //        }

        return $bundle;
    }

    public function loadBundleFiles($name)
    {
        echo __FUNCTION__ . PHP_EOL;
//        $this->jsFiles = $bundleFiles['js'];
        $this->cssFiles = ArrayHelper::merge($this->cssFiles, isset($this->assetBundles[$name]) ? $this->assetBundles[$name]['css'] : []);
    }

}