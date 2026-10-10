<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table         = 'collections';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'name',
        'slug',
        'description',
        'icon',
        'is_featured',
        'sort_order',
    ];
}
