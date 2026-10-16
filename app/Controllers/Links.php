<?php

namespace App\Controllers;

use App\Models\LinkModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

final class Links extends BaseController
{
    /**
     * Returns the visible link data consumed by the Learn Mode sidebar.
     */
    public function learn(int $id): ResponseInterface
    {
        $this->response->noCache();

        if (! $this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'AJAX requests only.']);
        }

        $link = $this->visibleLink($id);

        if ($link === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['error' => 'Link not found.']);
        }

        return $this->response->setJSON([
            'link' => [
                'id'                       => (int) $link['id'],
                'title'                    => (string) $link['title'],
                'url'                      => (string) $link['url'],
                'follow_url'               => site_url('go/' . $link['id']),
                'display_url'              => service('urlFormatter')->forDisplay((string) $link['url'], 64),
                'description'              => (string) ($link['description'] ?? ''),
                'collection_name'          => $link['collection_name'] === null
                    ? 'Unassigned'
                    : (string) $link['collection_name'],
                'is_favorite'              => (bool) $link['is_favorite'],
                'click_count'              => (int) $link['click_count'],
                'last_clicked_at'           => $link['last_clicked_at'],
                'created_at'                => $link['created_at'],
                'documentation'             => (string) ($link['documentation'] ?? ''),
                'documentation_updated_at'  => $link['documentation_updated_at'],
            ],
        ]);
    }

    /**
     * Records a click and immediately redirects to the saved destination.
     */
    public function follow(int $id): RedirectResponse
    {
        $links = new LinkModel();
        $link  = $this->visibleLink($id);

        if ($link === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $links->recordClick($id);

        return redirect()
            ->to((string) $link['url'], 302)
            ->noCache();
    }

    /**
     * Loads one link and applies the same visibility rules to Learn Mode and redirects.
     *
     * @return array<string, mixed>|null
     */
    private function visibleLink(int $id): ?array
    {
        $link = db_connect()
            ->table('links')
            ->select(
                'links.id, links.user_id, links.title, links.url, links.description, '
                . 'links.is_favorite, links.is_hidden, links.click_count, '
                . 'links.last_clicked_at, links.created_at, '
                . 'collections.name AS collection_name, '
                . 'collections.is_hidden AS collection_is_hidden, '
                . 'users.status AS owner_status, '
                . 'link_content.content AS documentation, '
                . 'link_content.updated_at AS documentation_updated_at',
            )
            ->join('collections', 'collections.id = links.collection_id', 'left')
            ->join('users', 'users.id = links.user_id')
            ->join('link_content', 'link_content.link_id = links.id', 'left')
            ->where('links.id', $id)
            ->get()
            ->getRowArray();

        if (! is_array($link) || ($link['owner_status'] ?? null) === 'banned') {
            return null;
        }

        $isHidden = (int) $link['is_hidden'] === 1
            || (int) ($link['collection_is_hidden'] ?? 0) === 1;
        $canSeeHidden = auth()->loggedIn()
            && (int) (auth()->user()->id ?? 0) === (int) $link['user_id'];

        return $isHidden && ! $canSeeHidden ? null : $link;
    }
}
