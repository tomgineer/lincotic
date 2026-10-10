<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $identity = $this->db->table('auth_identities')
            ->select('user_id')
            ->where('type', 'email_password')
            ->where('secret', 'tom@tomgineer.com')
            ->get()
            ->getRowArray();

        if ($identity === null) {
            throw new RuntimeException('Create tom@tomgineer.com before running DemoSeeder.');
        }

        $userId = (int) $identity['user_id'];
        $now    = date('Y-m-d H:i:s');

        $collections = [
            'development' => [
                'name'        => 'Development',
                'description' => 'Documentation, repositories, and tools for software development.',
                'icon'        => 'code-bracket',
                'is_featured' => 1,
                'sort_order'  => 10,
            ],
            'design' => [
                'name'        => 'Design',
                'description' => 'Design systems, inspiration, and interface resources.',
                'icon'        => 'swatch',
                'is_featured' => 1,
                'sort_order'  => 20,
            ],
            'learning' => [
                'name'        => 'Learning',
                'description' => 'Guides and references worth revisiting.',
                'icon'        => 'academic-cap',
                'is_featured' => 0,
                'sort_order'  => 30,
            ],
            'tools' => [
                'name'        => 'Tools',
                'description' => 'Useful web applications for everyday work.',
                'icon'        => 'wrench-screwdriver',
                'is_featured' => 0,
                'sort_order'  => 40,
            ],
        ];

        $collectionIds = [];
        $collectionTable = $this->db->table('collections');

        foreach ($collections as $slug => $collection) {
            $existing = $collectionTable
                ->select('id')
                ->where('user_id', $userId)
                ->where('slug', $slug)
                ->get()
                ->getRowArray();

            $data = [
                ...$collection,
                'user_id'    => $userId,
                'slug'       => $slug,
                'updated_at' => $now,
            ];

            if ($existing === null) {
                $collectionTable->insert([...$data, 'created_at' => $now]);
                $collectionIds[$slug] = (int) $this->db->insertID();
            } else {
                $collectionIds[$slug] = (int) $existing['id'];
                $collectionTable->where('id', $existing['id'])->update($data);
            }
        }

        $links = [
            [
                'collection'  => 'development',
                'title'       => 'GitHub',
                'url'         => 'https://github.com/',
                'description' => 'Code hosting and collaboration platform.',
                'importance'  => 'high',
                'is_favorite' => 1,
                'click_count' => 18,
            ],
            [
                'collection'  => 'development',
                'title'       => 'CodeIgniter User Guide',
                'url'         => 'https://codeigniter.com/user_guide/',
                'description' => 'Official documentation for CodeIgniter 4.',
                'importance'  => 'high',
                'is_favorite' => 1,
                'click_count' => 12,
            ],
            [
                'collection'  => 'development',
                'title'       => 'Tailwind CSS Documentation',
                'url'         => 'https://tailwindcss.com/docs',
                'description' => 'Utility-first CSS framework documentation.',
                'importance'  => 'normal',
                'is_favorite' => 1,
                'click_count' => 9,
            ],
            [
                'collection'  => 'development',
                'title'       => 'MDN Web Docs',
                'url'         => 'https://developer.mozilla.org/',
                'description' => 'Reference material for web technologies.',
                'importance'  => 'normal',
                'is_favorite' => 0,
                'click_count' => 7,
            ],
            [
                'collection'  => 'design',
                'title'       => 'daisyUI',
                'url'         => 'https://daisyui.com/',
                'description' => 'Component library for Tailwind CSS.',
                'importance'  => 'normal',
                'is_favorite' => 1,
                'click_count' => 11,
            ],
            [
                'collection'  => 'design',
                'title'       => 'Figma',
                'url'         => 'https://www.figma.com/',
                'description' => 'Collaborative interface design platform.',
                'importance'  => null,
                'is_favorite' => 0,
                'click_count' => 5,
            ],
            [
                'collection'  => 'design',
                'title'       => 'Heroicons',
                'url'         => 'https://heroicons.com/',
                'description' => 'A set of hand-crafted SVG icons.',
                'importance'  => 'low',
                'is_favorite' => 0,
                'click_count' => 3,
            ],
            [
                'collection'  => 'learning',
                'title'       => 'PHP Manual',
                'url'         => 'https://www.php.net/manual/en/',
                'description' => 'Official PHP language reference.',
                'importance'  => 'high',
                'is_favorite' => 1,
                'click_count' => 14,
            ],
            [
                'collection'  => 'learning',
                'title'       => 'web.dev',
                'url'         => 'https://web.dev/',
                'description' => 'Guidance for building modern web experiences.',
                'importance'  => 'normal',
                'is_favorite' => 0,
                'click_count' => 4,
            ],
            [
                'collection'  => 'tools',
                'title'       => 'Can I use',
                'url'         => 'https://caniuse.com/',
                'description' => 'Browser support tables for web platform features.',
                'importance'  => 'normal',
                'is_favorite' => 1,
                'click_count' => 8,
            ],
            [
                'collection'  => 'tools',
                'title'       => 'Regex101',
                'url'         => 'https://regex101.com/',
                'description' => 'Interactive regular expression tester and debugger.',
                'importance'  => 'low',
                'is_favorite' => 0,
                'click_count' => 2,
            ],
            [
                'collection'  => null,
                'title'       => 'Lincotic Repository',
                'url'         => 'https://github.com/tomgineer/lincotic',
                'description' => 'The source repository for Lincotic.',
                'importance'  => 'high',
                'is_favorite' => 1,
                'click_count' => 6,
            ],
        ];

        $linkTable = $this->db->table('links');

        foreach ($links as $link) {
            $collection = $link['collection'];
            unset($link['collection']);

            $existing = $linkTable
                ->select('id')
                ->where('user_id', $userId)
                ->where('url', $link['url'])
                ->get()
                ->getRowArray();

            $data = [
                ...$link,
                'user_id'         => $userId,
                'collection_id'   => $collection === null ? null : $collectionIds[$collection],
                'last_clicked_at' => $link['click_count'] > 0 ? $now : null,
                'updated_at'      => $now,
            ];

            if ($existing === null) {
                $linkTable->insert([...$data, 'created_at' => $now]);
            } else {
                $linkTable->where('id', $existing['id'])->update($data);
            }
        }
    }
}
