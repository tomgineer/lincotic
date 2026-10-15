<?php
    $canEditContent = $canEditContent ?? false;
    $canManageUsers = $canManageUsers ?? false;
?>

<ul class="flex h-full items-center">
    <?php if ($canEditContent): ?>
    <li class="h-full">
        <label class="block h-full" title="Edit mode">
            <input
                type="checkbox"
                class="peer sr-only"
                aria-label="Edit mode"
                data-edit-mode-toggle>
            <span class="edit-mode-trigger flex h-full cursor-pointer select-none items-center px-4 font-ui transition-colors hover:bg-base-200 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2">
                <span>Edit mode</span>
            </span>
        </label>
    </li>
    <li class="h-full">
        <button
            class="flex h-full cursor-pointer items-center px-4 text-acid transition-colors hover:bg-base-200"
            title="Add Link"
            type="button"
            data-add-link>
            <span>Add link</span>
        </button>
    </li>
    <?php endif ?>
    <?php if ($canManageUsers): ?>
        <li class="h-full">
            <button class="navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer hover:bg-base-200 transition-colors"
                title="Show Users"
                type="button"
                aria-controls="users-popover"
                popovertarget="users-popover">
                <span>Users</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </li>
    <?php endif ?>
    <?php if ($canEditContent): ?>
    <li class="h-full">
        <button class="admin-collections-popover-trigger navbar-popover-trigger flex h-full items-center gap-2 px-4 cursor-pointer hover:bg-base-200 transition-colors"
            type="button"
            aria-controls="admin-collections-popover"
            popovertarget="admin-collections-popover">
            <span>Collections</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="navbar-popover-chevron size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>
    </li>
    <?php endif ?>
</ul>

<?php if ($canManageUsers): ?>
    <?= $this->include('site/partials/navbar/popovers/users') ?>
<?php endif ?>
<?php if ($canEditContent): ?>
    <?= $this->include('site/partials/navbar/popovers/admin_collections') ?>
<?php endif ?>
