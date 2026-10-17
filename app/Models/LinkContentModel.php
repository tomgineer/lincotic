<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkContentModel extends Model {
    protected $table            = 'link_content';
    protected $primaryKey       = 'link_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'link_id',
        'content',
    ];
}
