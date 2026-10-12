<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $table          = 'links';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';
    protected $allowedFields  = [
        'user_id',
        'collection_id',
        'title',
        'url',
        'description',
        'importance',
        'is_favorite',
        'is_pending_review',
        'click_count',
        'last_clicked_at',
    ];
    protected $validationRules = [
        'importance' => 'permit_empty|in_list[low,medium,high]',
    ];

    /**
     * Records a visit without treating it as an edit to the link itself.
     */
    public function recordClick(int $id): bool
    {
        return $this->builder()
            ->where($this->primaryKey, $id)
            ->where($this->deletedField, null)
            ->set('click_count', 'click_count + 1', false)
            ->set('last_clicked_at', 'CURRENT_TIMESTAMP()', false)
            ->update();
    }
}
