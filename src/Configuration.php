<?php

namespace SadnessDeployer;

use Illuminate\Support\Collection;

class Configuration extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return array_get($this->items, $key, $default);
    }
}
