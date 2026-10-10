<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $table         = 'links';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
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
}
