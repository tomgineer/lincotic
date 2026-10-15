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
            ->select('links.user_id, links.url, links.is_hidden, collections.is_hidden AS collection_is_hidden, users.status AS owner_status')
            ->join('collections', 'collections.id = links.collection_id', 'left')
            ->join('users', 'users.id = links.user_id')
            ->find($id);

        $isHidden = $link !== null
            && ((int) $link['is_hidden'] === 1 || (int) ($link['collection_is_hidden'] ?? 0) === 1);
        $canSeeHidden = $link !== null
            && auth()->loggedIn()
            && (int) (auth()->user()->id ?? 0) === (int) $link['user_id'];

        if ($link === null
            || ($link['owner_status'] ?? null) === 'banned'
            || ($isHidden && ! $canSeeHidden)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $links->recordClick($id);

        return redirect()
            ->to((string) $link['url'], 302)
            ->noCache();
    }
}
