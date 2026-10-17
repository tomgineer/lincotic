<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model {
    protected $table         = 'collections';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'name',
        'description',
        'icon',
        'is_featured',
        'is_hidden',
        'sort_order',
    ];
}
