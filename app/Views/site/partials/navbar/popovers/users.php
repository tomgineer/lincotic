<section
    id="users-popover"
    class="navbar-popover"
    aria-labelledby="users-popover-heading"
    popover="auto">
    <div class="navbar-popover-content">
        <header class="navbar-popover-header">
            <div class="min-w-0">
                <h2 id="users-popover-heading" class="font-heading text-lg font-semibold">Users</h2>
                <p class="text-sm text-base-content/55">
                    <?= esc((string) count($adminUsers)) ?> <?= count($adminUsers) === 1 ? 'account' : 'accounts' ?>
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <button
                    type="button"
                    class="btn btn-sm btn-primary gap-2"
                    data-add-user>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                    </svg>
                    <span>Add user</span>
                </button>

                <button
                    type="button"
                    class="btn btn-square btn-ghost btn-sm"
                    aria-label="Close users"
                    popovertarget="users-popover"
                    popovertargetaction="hide">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
        </header>

        <div class="navbar-popover-body">
            <div class="overflow-x-auto rounded-xl border border-base-content/10 bg-base-50">
                <table class="w-full min-w-[42rem] table-fixed text-sm">
                    <colgroup>
                        <col>
                        <col class="w-36">
                        <col class="w-36">
                        <col class="w-24">
                    </colgroup>
                    <thead>
                        <tr class="border-b border-base-content/10 bg-base-200/25 text-left text-[10px] uppercase tracking-[0.16em] text-base-content/45">
                            <th class="px-5 py-3 font-semibold">User</th>
                            <th class="px-5 py-3 font-semibold">Role</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-content/10">
                        <?php if ($adminUsers === []): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <span class="mx-auto grid size-10 place-items-center rounded-full bg-base-200 text-base-content/45" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                        </svg>
                                    </span>
                                    <span class="mt-3 block text-base-content/55">No users found.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($adminUsers as $user): ?>
                                <tr class="group transition-colors hover:bg-base-100/60">
                                    <td class="px-5 py-3.5">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-base-100 text-base-content/55 transition-colors group-hover:bg-base-200" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block truncate font-heading font-semibold"><?= esc($user['username']) ?></span>
                                                <span class="mt-0.5 block truncate text-xs text-base-content/50"><?= esc($user['email']) ?></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= $user['role'] === 'admin' ? 'bg-acid/10 text-acid' : 'bg-base-200 text-base-content/65' ?>">
                                            <?= esc(ucfirst($user['role'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-2 text-xs font-medium <?= $user['active'] ? 'text-success' : 'text-error' ?>">
                                            <span class="size-1.5 rounded-full <?= $user['active'] ? 'bg-success' : 'bg-error' ?>" aria-hidden="true"></span>
                                            <?= $user['active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-ghost gap-1.5 text-base-content/60 hover:btn-primary"
                                            aria-label="Edit <?= esc($user['username'], 'attr') ?>"
                                            aria-controls="edit-user-modal"
                                            aria-haspopup="dialog"
                                            data-edit-user
                                            data-user-id="<?= esc((string) $user['id'], 'attr') ?>"
                                            data-user-username="<?= esc($user['username'], 'attr') ?>"
                                            data-user-email="<?= esc($user['email'], 'attr') ?>"
                                            data-user-role="<?= esc($user['role'], 'attr') ?>"
                                            data-user-active="<?= $user['active'] ? '1' : '0' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>