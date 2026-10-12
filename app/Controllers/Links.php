<?php

namespace App\Controllers;

use App\Models\LinkModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

final class Links extends BaseController
{
    /**
     * Records a click and immediately redirects to the saved destination.
     */
    public function follow(int $id): RedirectResponse
    {
        $links = new LinkModel();
        $link  = $links->select('url')->find($id);

        if ($link === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $links->recordClick($id);

        return redirect()
            ->to((string) $link['url'], 302)
            ->noCache();
    }
}
