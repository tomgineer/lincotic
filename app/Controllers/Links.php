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
        $link  = $links
            ->select('links.url, links.is_hidden, collections.is_hidden AS collection_is_hidden')
            ->join('collections', 'collections.id = links.collection_id', 'left')
            ->find($id);

        $isHiddenFromGuest = $link !== null
            && ! auth()->loggedIn()
            && ((int) $link['is_hidden'] === 1 || (int) ($link['collection_is_hidden'] ?? 0) === 1);

        if ($link === null || $isHiddenFromGuest) {
            throw PageNotFoundException::forPageNotFound();
        }

        $links->recordClick($id);

        return redirect()
            ->to((string) $link['url'], 302)
            ->noCache();
    }
}
