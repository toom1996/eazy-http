<?php

namespace eazy\http;

class Scanner extends Component
{
    public array $provider = [];
    
    public function init()
    {
        foreach ($this->provider as $item) {
            $provider = Eazy::createObject($item[0]);

        }
    }
}