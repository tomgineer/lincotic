<?= $this->extend('site/layouts/default') ?>

<?= $this->section('title') ?>Hash Generator | Lincotic<?= $this->endSection() ?>

<?= $this->section('header') ?>
<?= view('site/partials/utilities/header', [
    'eyebrow'     => 'Developer utility',
    'description' => 'Create common message digests from any text.',
]) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main
    class="mx-auto w-full max-w-6xl px-6 py-10"
    data-hashing-utility
    data-hash-url="<?= esc(site_url('hash-generator/generate'), 'attr') ?>"
    data-csrf-header="<?= esc(config('Security')->headerName, 'attr') ?>"
    data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>">
    <section class="utility-hero">
        <div class="max-w-2xl">
            <p class="font-mono text-sm font-bold uppercase tracking-[0.16em] text-acid">Hash lab</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight sm:text-5xl">One input. Five fingerprints.</h1>
            <p class="mt-4 max-w-xl text-lg text-base-content/65">
                Generate MD5 and SHA digests together. Processing happens on this server and results are never stored.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2 rounded-full border border-base-content/15 bg-base-100/60 px-4 py-2 text-sm text-base-content/60">
            <span class="size-2 rounded-full bg-acid"></span>
            Server-side PHP
        </div>
    </section>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
        <section class="utility-panel p-6" aria-labelledby="hash-input-title">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="hash-input-title" class="text-2xl font-bold">Source text</h2>
                    <p class="mt-2 text-sm text-base-content/55">Whitespace is preserved unless trimming is enabled.</p>
                </div>
                <span class="badge badge-outline font-mono" data-hash-byte-count>0 bytes</span>
            </div>

            <label class="mt-6 block" for="hash-source">
                <span class="sr-only">Text to hash</span>
                <textarea
                    id="hash-source"
                    class="textarea min-h-52 w-full resize-y bg-base-100 font-mono text-base"
                    maxlength="100000"
                    placeholder="Type or paste text here…"
                    spellcheck="false"
                    data-hash-input></textarea>
            </label>

            <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm text-base-content/70">
                <input type="checkbox" class="checkbox checkbox-sm" checked data-hash-trim>
                Trim whitespace before hashing
            </label>

            <p class="mt-4 min-h-5 text-sm text-base-content/55" aria-live="polite" data-hash-status>Waiting for input.</p>

            <div class="mt-5 rounded-xl border border-warning/25 bg-warning/10 p-4 text-sm text-base-content/70" role="note">
                <strong class="text-warning">Security note:</strong>
                MD5 and SHA-1 are legacy digests. Do not use them for password storage, signatures, or other security-sensitive purposes.
            </div>
        </section>

        <section class="utility-panel" aria-labelledby="hash-output-title">
            <header class="border-b border-base-content/10 p-6">
                <h2 id="hash-output-title" class="text-2xl font-bold">Digests</h2>
                <p class="mt-2 text-sm text-base-content/55">Copy any result with one click.</p>
            </header>

            <div class="hash-list">
                <?php foreach ($algorithms as $algorithm => $label): ?>
                    <div class="hash-row" data-copy-entry>
                        <label for="hash-<?= esc($algorithm, 'attr') ?>" class="font-mono text-sm font-bold text-base-content/70">
                            <?= esc($label) ?>
                        </label>
                        <div class="utility-output">
                            <input
                                id="hash-<?= esc($algorithm, 'attr') ?>"
                                type="text"
                                placeholder="<?= esc($label, 'attr') ?> will appear here"
                                readonly
                                spellcheck="false"
                                data-hash-output="<?= esc($algorithm, 'attr') ?>">
                            <button
                                type="button"
                                class="utility-copy"
                                aria-label="Copy <?= esc($label, 'attr') ?> hash"
                                disabled
                                data-copy-hash="<?= esc($algorithm, 'attr') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
    </div>
</main>
<?= $this->endSection() ?>
