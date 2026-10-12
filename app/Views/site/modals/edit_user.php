<dialog
    id="edit-user-modal"
    class="modal"
    aria-labelledby="edit-user-modal-title"
    aria-describedby="edit-user-modal-description"
    data-edit-user-modal
    data-create-endpoint="<?= esc(site_url('edit/users'), 'attr') ?>"
    data-csrf-header="<?= esc(config('Security')->headerName, 'attr') ?>"
    data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>">
    <form class="modal-box w-[calc(100%-2rem)] max-w-2xl overflow-hidden p-0" data-edit-user-form>
        <header class="flex items-start justify-between gap-6 border-b border-base-content/10 px-6 py-5 sm:px-8">
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-primary/15 text-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3z" /></svg>
                </span>

                <div class="min-w-0">
                    <h2 id="edit-user-modal-title" class="font-heading text-xl font-semibold">Add user</h2>
                    <p id="edit-user-modal-description" class="mt-1 text-sm text-base-content/60">
                        Create a new account and choose its access level.
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm shrink-0"
                commandfor="edit-user-modal"
                command="close"
                aria-label="Close user editor">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="max-h-[70vh] space-y-6 overflow-y-auto px-6 py-6 sm:px-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Username</legend>
                    <label class="input w-full bg-base-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-base-content/45" aria-hidden="true"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        <input
                            type="text"
                            name="username"
                            placeholder="username"
                            autocomplete="username"
                            spellcheck="false"
                            minlength="3"
                            maxlength="30"
                            pattern="[A-Za-z0-9.]+"
                            required
                            data-edit-user-control>
                    </label>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Email address</legend>
                    <label class="input w-full bg-base-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-base-content/45" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="m3 7 9 6 9 -6" /></svg>
                        <input
                            type="email"
                            name="email"
                            placeholder="name@example.com"
                            autocomplete="email"
                            spellcheck="false"
                            maxlength="254"
                            required
                            data-edit-user-control>
                    </label>
                </fieldset>

                <fieldset class="fieldset sm:col-span-2">
                    <legend class="fieldset-legend font-heading">Password</legend>
                    <label class="input w-full bg-base-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-base-content/45" aria-hidden="true"><path d="M16 8a4 4 0 1 0 -7.9 1" /><path d="M3 21h6v-3h3v-3h3l2.5 -2.5" /></svg>
                        <input
                            type="password"
                            name="password"
                            placeholder="Create a strong password"
                            autocomplete="new-password"
                            minlength="<?= esc((string) config('Auth')->minimumPasswordLength, 'attr') ?>"
                            required
                            data-edit-user-control>
                    </label>
                    <p class="label text-xs text-base-content/50">Use at least <?= esc((string) config('Auth')->minimumPasswordLength) ?> characters.</p>
                </fieldset>
            </div>

            <div>
                <h3 class="font-heading text-sm font-semibold">Access</h3>
                <p class="mt-1 text-xs text-base-content/55">Control what this user can access and whether they can sign in.</p>

                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <fieldset class="rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                        <legend class="sr-only">Account role</legend>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 text-secondary" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M12 3l7 4v5c0 5 -3 8 -7 9c-4 -1 -7 -4 -7 -9v-5z" /><path d="m9 12 2 2 4 -4" /></svg>
                            </span>
                            <span>
                                <span class="block font-heading text-sm font-semibold">Account role</span>
                                <span class="mt-0.5 block text-xs leading-5 text-base-content/55">Choose the user's level of access.</span>
                            </span>
                        </div>

                        <div class="join mt-3 flex w-full">
                            <input
                                type="radio"
                                name="role"
                                value="user"
                                class="btn btn-soft join-item flex-1"
                                aria-label="User"
                                checked
                                data-edit-user-control>
                            <input
                                type="radio"
                                name="role"
                                value="admin"
                                class="btn btn-soft join-item flex-1"
                                aria-label="Administrator"
                                data-edit-user-control>
                        </div>
                    </fieldset>

                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                        <span class="flex min-w-0 items-start gap-3">
                            <span class="mt-0.5 text-success" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M12 3a9 9 0 1 0 9 9" /><path d="M12 7v5l3 2" /><path d="m16 5 2 2 4 -4" /></svg>
                            </span>
                            <span>
                                <span class="block font-heading text-sm font-semibold">Active account</span>
                                <span class="mt-0.5 block text-xs leading-5 text-base-content/55">Allow this user to sign in.</span>
                            </span>
                        </span>
                        <input type="checkbox" name="active" value="1" class="toggle toggle-success shrink-0" checked data-edit-user-control>
                    </label>
                </div>
            </div>
        </div>

        <footer class="flex flex-wrap items-center gap-3 border-t border-base-content/10 bg-base-200/40 px-6 py-4 sm:px-8">
            <button type="button" class="btn btn-square btn-ghost text-base-content/55 hover:btn-error hover:text-white" aria-label="Delete user" hidden data-edit-user-delete>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </button>

            <p class="min-h-5 flex-1 text-sm text-base-content/60" aria-live="polite" data-edit-user-status></p>

            <button
                type="button"
                class="btn btn-ghost"
                commandfor="edit-user-modal"
                command="close">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary min-w-32" data-edit-user-save>Add user</button>
        </footer>
    </form>

    <div class="modal-backdrop">
        <button
            type="button"
            commandfor="edit-user-modal"
            command="close"
            aria-label="Close user editor">Close</button>
    </div>
</dialog>
