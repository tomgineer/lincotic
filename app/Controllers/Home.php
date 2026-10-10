<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\LinkModel;

class Home extends BaseController
{
    public function index(): string
    {
        $collections = (new CollectionModel())
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $links = (new LinkModel())
            ->orderBy('title', 'ASC')
            ->findAll();

        $linksByCollection = [];

        foreach ($links as $link) {
            if ($link['collection_id'] !== null) {
                $linksByCollection[$link['collection_id']][] = $link;
            }
        }

        foreach ($collections as &$collection) {
            $collection['links'] = $linksByCollection[$collection['id']] ?? [];
        }
        unset($collection);

        return view('site/pages/home', ['collections' => $collections]);
    }
}
