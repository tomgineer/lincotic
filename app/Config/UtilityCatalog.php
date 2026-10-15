<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

final class UtilityCatalog extends BaseConfig
{
    /**
     * @var list<array{path: string, active: string, title: string, description: string}>
     */
    public array $items = [
        [
            'path'        => 'password-generator',
            'active'      => 'password-generator',
            'title'       => 'Password generator',
            'description' => 'Generate fresh standard and strong passwords in multiple lengths.',
        ],
        [
            'path'        => 'date-time-calculator',
            'active'      => 'date-time-calculator',
            'title'       => 'Date & time calculator',
            'description' => 'View live time, calendar progress, countdowns, and milestones.',
        ],
        [
            'path'        => 'hash-generator',
            'active'      => 'hash-generator*',
            'title'       => 'Hash generator',
            'description' => 'Generate MD5 and SHA digests from any text.',
        ],
        [
            'path'        => 'work-time-tracker',
            'active'      => 'work-time-tracker',
            'title'       => 'Work time tracker',
            'description' => 'Track an eight-hour workday with automatic break deductions.',
        ],
    ];
}
