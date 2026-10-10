<?php

namespace App\Controllers;

use App\Services\CollectionService;

class Home extends BaseController {

    public function index(): string {
        $collections = (new CollectionService())->getWithLinks();

        return view('site/pages/home', ['collections' => $collections]);
    }
}
