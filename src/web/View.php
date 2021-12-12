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
        var_dump($this->cssFiles);
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
        var_dump($lines);
        return empty($lines) ? '' : implode("\n", $lines);
    }

    public function registerJs()
    {

    }

    public function registerFile()
    {

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
        var_dump($this->assetBundles);
        $this->loadBundleFiles($name);
        return $this->assetBundles[$name];
    }

    public function registerBundleCssFiles(AssetBundle $bundle)
    {
        foreach ($bundle->css as $file) {
            $assetBundle[$bundle::class]['css'][] = HtmlHelper::cssFile($file);
        }

        $this->assetBundles = ArrayHelper::merge($this->assetBundles, $assetBundle);
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
        $bundleFiles = $this->assetBundles[$name];
//        $this->jsFiles = $bundleFiles['js'];
        $this->cssFiles = $bundleFiles['css'];
    }
}