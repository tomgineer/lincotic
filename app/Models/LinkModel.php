<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $table         = 'links';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $beforeInsert  = ['normalizeImportance'];
    protected $beforeUpdate  = ['normalizeImportance'];
    protected $allowedFields = [
        'user_id',
        'collection_id',
        'title',
        'url',
        'description',
        'importance',
        'is_favorite',
        'click_count',
        'last_clicked_at',
    ];
    protected $validationRules = [
        'importance' => 'permit_empty|in_list[low,normal,high]',
    ];

    protected function normalizeImportance(array $data): array
    {
        if (
            isset($data['data'])
            && is_array($data['data'])
            && array_key_exists('importance', $data['data'])
            && is_string($data['data']['importance'])
            && trim($data['data']['importance']) === ''
        ) {
            $data['data']['importance'] = null;
        }

        return $data;
    }
}
