<div
    class="flex flex-wrap items-center gap-1 border-b border-base-content/15 bg-base-200/50 p-2"
    role="toolbar"
    aria-label="Documentation formatting"
    data-editor-toolbar>
    <label class="flex items-center gap-2">
        <span class="sr-only">Text style</span>
        <select
            class="select select-sm w-32 bg-base-100"
            aria-label="Text style"
            data-editor-control
            data-editor-block-style>
            <option value="paragraph">Paragraph</option>
            <?php for ($level = 1; $level <= 6; $level++): ?>
                <option value="heading-<?= $level ?>">Heading <?= $level ?></option>
            <?php endfor ?>
        </select>
    </label>

    <span class="mx-1 h-6 w-px bg-base-content/15" aria-hidden="true"></span>

    <div class="join" role="group" aria-label="Text formatting">
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Bold" aria-label="Bold" data-editor-control data-editor-command="bold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 5h6a3.5 3.5 0 0 1 0 7h-6z" /><path d="M13 12h1a3.5 3.5 0 0 1 0 7h-7v-7" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Italic" aria-label="Italic" data-editor-control data-editor-command="italic">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M11 5h6" /><path d="M7 19h6" /><path d="M14 5l-4 14" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Underline" aria-label="Underline" data-editor-control data-editor-command="underline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 5v5a5 5 0 0 0 10 0v-5" /><path d="M5 19h14" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Strikethrough" aria-label="Strikethrough" data-editor-control data-editor-command="strike">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 12h14" /><path d="M16 6.5a4 2 0 0 0 -4 -1.5h-1a3 3 0 0 0 -2.83 4" /><path d="M8 16.5a4 2 0 0 0 4 1.5h1a3 3 0 0 0 2.83 -4" /></svg>
        </button>
    </div>

    <span class="mx-1 h-6 w-px bg-base-content/15" aria-hidden="true"></span>

    <div class="join" role="group" aria-label="Lists">
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Bullet list" aria-label="Bullet list" data-editor-control data-editor-command="bullet-list">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 6h11" /><path d="M9 12h11" /><path d="M9 18h11" /><path d="M5 6v.01" /><path d="M5 12v.01" /><path d="M5 18v.01" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Numbered list" aria-label="Numbered list" data-editor-control data-editor-command="ordered-list">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M11 6h9" /><path d="M11 12h9" /><path d="M12 18h8" /><path d="M4 16a2 2 0 1 1 4 0c0 1 -.5 1.5 -4 3h4" /><path d="M6 10v-6l-2 2" /></svg>
        </button>
    </div>

    <span class="mx-1 h-6 w-px bg-base-content/15" aria-hidden="true"></span>

    <div class="join" role="group" aria-label="Blocks">
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Blockquote" aria-label="Blockquote" data-editor-control data-editor-command="blockquote">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 11h-4a2 2 0 0 0 -2 2v1a2 2 0 0 0 2 2h1a3 3 0 0 0 3 -3v-5a4 4 0 0 0 -4 -4" /><path d="M20 11h-4a2 2 0 0 0 -2 2v1a2 2 0 0 0 2 2h1a3 3 0 0 0 3 -3v-5a4 4 0 0 0 -4 -4" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Code block" aria-label="Code block" data-editor-control data-editor-command="code-block">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 8l-4 4l4 4" /><path d="M17 8l4 4l-4 4" /><path d="M14 4l-4 16" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Horizontal rule" aria-label="Horizontal rule" data-editor-control data-editor-command="horizontal-rule">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 12h14" /></svg>
        </button>
    </div>

    <span class="mx-1 h-6 w-px bg-base-content/15" aria-hidden="true"></span>

    <div class="join" role="group" aria-label="History">
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Undo" aria-label="Undo" data-editor-control data-editor-command="undo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
        </button>
        <button type="button" class="btn btn-square btn-ghost btn-sm join-item" title="Redo" aria-label="Redo" data-editor-control data-editor-command="redo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 14l4 -4l-4 -4" /><path d="M19 10h-11a4 4 0 1 0 0 8h1" /></svg>
        </button>
    </div>
</div>
