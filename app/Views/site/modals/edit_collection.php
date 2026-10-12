<dialog
    id="edit-collection-modal"
    class="modal"
    aria-labelledby="edit-collection-modal-title"
    aria-describedby="edit-collection-modal-description"
    data-edit-collection-modal
    data-endpoint-template="<?= esc(site_url('edit/collections/__COLLECTION_ID__'), 'attr') ?>"
    data-csrf-header="<?= esc(config('Security')->headerName, 'attr') ?>"
    data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>">
    <form class="modal-box w-[calc(100%-2rem)] max-w-2xl overflow-hidden p-0" data-edit-collection-form>
        <input type="hidden" name="id">

        <header class="flex items-start justify-between gap-6 border-b border-base-content/10 px-6 py-5 sm:px-8">
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-primary/15 text-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 7a3 3 0 0 1 3 -3h3l3 3h6a3 3 0 0 1 3 3v7a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M13.5 13.5l2 -2a1.414 1.414 0 0 1 2 2l-2 2" /><path d="M14 15l-1 1v2h2l1 -1" /></svg>
                </span>

                <div class="min-w-0">
                    <h2 id="edit-collection-modal-title" class="font-heading text-xl font-semibold">Edit collection</h2>
                    <p id="edit-collection-modal-description" class="mt-1 text-sm text-base-content/60">
                        Update how this collection is named and presented.
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-square btn-ghost btn-sm shrink-0"
                commandfor="edit-collection-modal"
                command="close"
                aria-label="Close collection editor">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
            </button>
        </header>

        <div class="space-y-5 px-6 py-6 sm:px-8">
            <div class="grid grid-cols-1 gap-5">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Name</legend>
                    <input
                        type="text"
                        name="name"
                        class="input w-full bg-base-100"
                        placeholder="Collection name"
                        autocomplete="off"
                        maxlength="120"
                        required
                        data-edit-collection-control>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Slug</legend>
                    <label class="input w-full bg-base-100">
                        <span class="font-mono text-sm text-base-content/40" aria-hidden="true">/</span>
                        <input
                            type="text"
                            name="slug"
                            placeholder="collection-name"
                            autocomplete="off"
                            spellcheck="false"
                            maxlength="150"
                            pattern="[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*"
                            required
                            data-edit-collection-control>
                    </label>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Description</legend>
                    <textarea
                        name="description"
                        class="textarea h-32 w-full resize-none overflow-y-auto bg-base-100"
                        placeholder="Add an optional description for this collection."
                        maxlength="65535"
                        data-edit-collection-control></textarea>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend font-heading">Icon SVG</legend>
                    <textarea
                        name="icon"
                        class="textarea h-32 w-full resize-none overflow-y-auto bg-base-100 font-mono text-xs leading-relaxed"
                        placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot; ...&gt;...&lt;/svg&gt;"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="65535"
                        data-edit-collection-control></textarea>
                </fieldset>
            </div>

            <label class="flex cursor-pointer items-center justify-between gap-5 rounded-xl border border-base-content/10 bg-base-100 px-4 py-4">
                <span class="flex min-w-0 items-start gap-3">
                    <span class="mt-0.5 text-secondary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                    </span>
                    <span>
                        <span class="block font-heading text-sm font-semibold">Featured collection</span>
                        <span class="mt-0.5 block text-xs text-base-content/55">Show this collection in the default home view.</span>
                    </span>
                </span>
                <input type="checkbox" name="is_featured" value="1" class="toggle toggle-secondary shrink-0" data-edit-collection-control>
            </label>
        </div>

        <footer class="flex flex-wrap items-center gap-3 border-t border-base-content/10 bg-base-200/40 px-6 py-4 sm:px-8">
            <button
                type="button"
                class="btn btn-square btn-ghost text-base-content/55 hover:btn-error hover:text-white"
                aria-label="Delete collection"
                data-edit-collection-control
                data-edit-collection-delete>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </button>

            <p class="min-h-5 flex-1 text-sm text-base-content/60" aria-live="polite" data-edit-collection-status></p>

            <button
                type="button"
                class="btn btn-ghost"
                commandfor="edit-collection-modal"
                command="close">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary min-w-32" data-edit-collection-save>Save changes</button>
        </footer>
    </form>

    <div class="modal-backdrop">
        <button
            type="button"
            commandfor="edit-collection-modal"
            command="close"
            aria-label="Close collection editor">Close</button>
    </div>
</dialog>
