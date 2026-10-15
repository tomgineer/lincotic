<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Lincotic extends BaseConfig
{
    public string $rootUsername = 'civic';

    /**
     * @var array{enabled: bool, duration: int}
     */
    public array $cache = [
        'enabled'  => true,
        'duration' => 3600,
    ];
}
