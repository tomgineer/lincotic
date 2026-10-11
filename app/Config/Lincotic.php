<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Lincotic extends BaseConfig
{
    /**
     * @var array{enabled: bool, duration: int}
     */
    public array $cache = [
        'enabled'  => false,
        'duration' => 3600,
    ];
}
