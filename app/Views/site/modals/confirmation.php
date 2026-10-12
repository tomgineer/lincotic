<dialog
    id="confirmation-modal"
    class="modal"
    aria-labelledby="confirmation-modal-title"
    aria-describedby="confirmation-modal-description">
    <div class="modal-box w-[calc(100%-2rem)] max-w-md overflow-hidden p-0">
        <div class="px-6 pt-7 pb-6 sm:px-8">
            <span class="grid size-12 place-items-center rounded-full bg-error/15 text-error" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0" /><path d="M12 16h.01" /></svg>
            </span>

            <h2 id="confirmation-modal-title" class="mt-5 font-heading text-xl font-semibold">
                Are you sure?
            </h2>
            <p id="confirmation-modal-description" class="mt-2 text-sm leading-6 text-base-content/65">
                This action cannot be undone. Please confirm that you want to continue.
            </p>
        </div>

        <footer class="flex items-center justify-end gap-3 border-t border-base-content/10 bg-base-200/40 px-6 py-4 sm:px-8">
            <button
                type="button"
                class="btn btn-ghost"
                commandfor="confirmation-modal"
                command="close">
                Cancel
            </button>
            <button type="button" class="btn btn-error min-w-28">Confirm</button>
        </footer>
    </div>

    <div class="modal-backdrop">
        <button
            type="button"
            commandfor="confirmation-modal"
            command="close"
            aria-label="Cancel confirmation">Cancel</button>
    </div>
</dialog>
