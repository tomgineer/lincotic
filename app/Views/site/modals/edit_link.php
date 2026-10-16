<dialog
    id="edit-link-modal"
    class="modal"
    aria-labelledby="edit-link-modal-title"
    aria-describedby="edit-link-modal-description"
    data-edit-link-modal
    data-create-endpoint="<?= esc(site_url('edit/links'), 'attr') ?>"
    data-endpoint-template="<?= esc(site_url('edit/links/__LINK_ID__'), 'attr') ?>"
    data-csrf-header="<?= esc(config('Security')->headerName, 'attr') ?>"
    data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>">
    <form class="modal-box relative sm:-top-[5vh] w-[calc(100%-2rem)] max-w-4xl overflow-hidden p-0" data-edit-link-form>
        <input type="hidden" name="id">

        <header class="flex items-start justify-between gap-6 border-b border-base-content/10 px-6 py-5 sm:px-8">
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-primary/15 text-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                </span>

                <div class="min-w-0">
                    <h2 id="edit-link-modal-title" class="font-heading text-xl font-semibold" data-edit-link-title>Edit link</h2>
                    <p id="edit-link-modal-description" class="mt-1 text-sm text-base-content/60" data-edit-link-description>
                        Update the destination, organization, and status of this link.
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm shrink-0"
                commandfor="edit-link-modal"
                command="close"
                aria-label="Close link editor">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div role="tablist" class="tabs tabs-border">
            <input
                type="radio"
                name="edit_link_tab"
                class="tab ml-6 font-heading sm:ml-8"
                aria-label="Details"
                checked
                data-edit-link-control
                data-edit-link-tab
                data-edit-link-details-tab>

            <div role="tabpanel" class="tab-content">
                <div class="max-h-[70vh] space-y-5 overflow-y-auto px-6 py-6 sm:px-8">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <fieldset class="fieldset sm:col-span-2">
                    <legend class="fieldset-legend font-heading">Title</legend>
                    <input
                        type="text"
                        name="title"
                        class="input w-full bg-base-100"
                        placeholder="Link title"
                        autocomplete="off"
                        autofocus
                        maxlength="255"
                        required
                        data-edit-link-control>
                </fieldset>

                <fieldset class="fieldset sm:col-span-2">
                    <legend class="fieldset-legend font-heading">URL</legend>
                    <div class="join w-full">
                        <label class="input join-item min-w-0 flex-1 bg-base-100">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-base-content/45" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                            <input
                                type="url"
                                name="url"
                                placeholder="https://example.com"
                                autocomplete="url"
                                spellcheck="false"
                                maxlength="2048"
                                required
                                data-edit-link-control>
                        </label>
                        <button
                            type="button"
                            class="btn btn-square btn-soft join-item text-base-content/60 hover:text-primary"
                            title="Paste URL from clipboard"
                            aria-label="Paste URL from clipboard"
                            data-edit-link-control
                            data-edit-link-paste-url>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2" /></svg>
                        </button>
                        <button
                            type="button"
                            class="btn btn-square btn-soft join-item text-base-content/60 hover:text-primary"
                            title="Open link in a new tab"
                            aria-label="Open link in a new tab"
                            data-edit-link-control
                            data-edit-link-open-url>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                        </button>
                    </div>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Collection</legend>
                    <select name="collection_id" class="select w-full bg-base-100" required data-edit-link-control>
                        <?php foreach ($collectionOptions ?? [] as $collectionOption): ?>
                            <option value="<?= esc((string) $collectionOption['id'], 'attr') ?>">
                                <?= esc($collectionOption['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Importance</legend>
                    <div class="join w-full">
                        <input type="radio" name="importance" value="" class="btn btn-soft join-item flex-1" aria-label="Default" checked data-edit-link-control>
                        <input type="radio" name="importance" value="low" class="btn btn-soft join-item flex-1" aria-label="Low" data-edit-link-control>
                        <input type="radio" name="importance" value="medium" class="btn btn-soft join-item flex-1" aria-label="Medium" data-edit-link-control>
                        <input type="radio" name="importance" value="high" class="btn btn-soft join-item flex-1" aria-label="High" data-edit-link-control>
                    </div>
                </fieldset>

                <fieldset class="fieldset sm:col-span-2">
                    <legend class="fieldset-legend font-heading">Description</legend>
                    <textarea
                        name="description"
                        class="textarea h-32 w-full resize-none overflow-y-auto bg-base-100"
                        placeholder="Add an optional note about this link."
                        maxlength="65535"
                        data-edit-link-control></textarea>
                </fieldset>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                            <span class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 text-red-400" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                                </span>
                                <span>
                                    <span class="block font-heading text-sm font-semibold">Favorite</span>
                                    <span class="mt-0.5 block text-xs text-base-content/55">Keep this link easy to find.</span>
                                </span>
                            </span>
                            <input type="checkbox" name="is_favorite" value="1" class="toggle toggle-primary shrink-0" data-edit-link-control>
                        </label>

                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                            <span class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 text-orange-400" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0" /><path d="M12 16h.01" /></svg>
                                </span>
                                <span>
                                    <span class="block font-heading text-sm font-semibold">Pending review</span>
                                    <span class="mt-0.5 block text-xs text-base-content/55">Mark it for a later check.</span>
                                </span>
                            </span>
                            <input type="checkbox" name="is_pending_review" value="1" class="toggle toggle-primary shrink-0" data-edit-link-control>
                        </label>

                        <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                            <span class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 text-blue-400" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                </span>
                                <span>
                                    <span class="block font-heading text-sm font-semibold">Hidden</span>
                                    <span class="mt-0.5 block text-xs text-base-content/55">Show only to logged-in users.</span>
                                </span>
                            </span>
                            <input type="checkbox" name="is_hidden" value="1" class="toggle toggle-primary shrink-0" data-edit-link-control>
                        </label>
                    </div>
                </div>
            </div>

            <input
                type="radio"
                name="edit_link_tab"
                class="tab font-heading"
                aria-label="Documentation"
                data-edit-link-control
                data-edit-link-tab>

            <div role="tabpanel" class="tab-content">
                <div class="max-h-[70vh] overflow-y-auto px-6 py-6 sm:px-8">
                    <section aria-labelledby="edit-link-documentation-title">
                        <div class="mb-3">
                            <h3 id="edit-link-documentation-title" class="font-heading text-base font-semibold">Documentation</h3>
                            <p class="text-sm text-base-content/55">
                                Add long-form notes, instructions, or reference material for this link.
                            </p>
                        </div>

                        <div class="overflow-hidden rounded-box border border-base-content/15 bg-base-100">
                            <?= $this->include('site/partials/editor') ?>

                            <textarea
                                name="documentation"
                                class="textarea min-h-[48vh] w-full resize-none rounded-none border-0 bg-base-100 p-5 leading-7 focus:outline-none"
                                placeholder="Start writing the documentation for this link…"
                                spellcheck="true"
                                data-edit-link-control
                                data-edit-link-documentation></textarea>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="flex flex-wrap items-center gap-3 border-t border-base-content/10 bg-base-200/40 px-6 py-4 sm:px-8">
            <button
                type="button"
                class="btn btn-square btn-ghost text-base-content/55 hover:btn-error hover:text-white"
                aria-label="Delete link"
                data-edit-link-control
                data-edit-link-delete>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </button>

            <p class="min-h-5 flex-1 text-sm text-base-content/60" aria-live="polite" data-edit-link-status></p>

            <button
                type="button"
                class="btn btn-ghost"
                commandfor="edit-link-modal"
                command="close">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary min-w-32" data-edit-link-save>Save changes</button>
        </footer>
    </form>

    <div class="modal-backdrop">
        <button
            type="button"
            commandfor="edit-link-modal"
            command="close"
            aria-label="Close link editor">Close</button>
    </div>
</dialog>
