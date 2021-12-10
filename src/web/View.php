<?php

namespace eazy\http\web;

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
        if (!empty($this->metaTags)) {
            $lines[] = implode("\n", $this->metaTags);
        }

        if (!empty($this->linkTags)) {
            $lines[] = implode("\n", $this->linkTags);
        }
        if (!empty($this->cssFiles)) {
            $lines[] = implode("\n", $this->cssFiles);
        }
        if (!empty($this->css)) {
            $lines[] = implode("\n", $this->css);
        }
        if (!empty($this->jsFiles[self::POS_HEAD])) {
            $lines[] = implode("\n", $this->jsFiles[self::POS_HEAD]);
        }
        if (!empty($this->js[self::POS_HEAD])) {
            $lines[] = Html::script(implode("\n", $this->js[self::POS_HEAD]));
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }
}