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
                class="btn btn-primary gap-2"
                data-add-user>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                <span>Add user</span>
            </button>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm"
                aria-label="Close users"
                popovertarget="users-popover"
                popovertargetaction="hide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </div>
    </header>

    <div class="navbar-popover-body">
        <div class="overflow-x-auto rounded-box border border-base-content/10">
        <table class="table table-zebra">
            <thead class="bg-base-100">
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="w-0"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($adminUsers === []): ?>
                    <tr>
                        <td colspan="4" class="py-10 text-center text-base-content/55">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($adminUsers as $user): ?>
                        <tr>
                            <td>
                                <div class="font-medium"><?= esc($user['username']) ?></div>
                                <div class="mt-0.5 text-xs text-base-content/55"><?= esc($user['email']) ?></div>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm"><?= esc(ucfirst($user['role'])) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-sm <?= $user['active'] ? 'badge-success' : 'badge-error' ?>">
                                    <?= $user['active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="w-0 whitespace-nowrap text-right">
                                <button
                                    type="button"
                                    class="btn btn-square btn-ghost btn-sm hover:btn-primary"
                                    aria-label="Edit <?= esc($user['username'], 'attr') ?>"
                                    aria-controls="edit-user-modal"
                                    aria-haspopup="dialog"
                                    data-edit-user
                                    data-user-id="<?= esc((string) $user['id'], 'attr') ?>"
                                    data-user-username="<?= esc($user['username'], 'attr') ?>"
                                    data-user-email="<?= esc($user['email'], 'attr') ?>"
                                    data-user-role="<?= esc($user['role'], 'attr') ?>"
                                    data-user-active="<?= $user['active'] ? '1' : '0' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39" /></svg>
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
