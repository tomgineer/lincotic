<?= $this->extend('site/layouts/default') ?>

<?= $this->section('title') ?>Password Generator | Lincotic<?= $this->endSection() ?>

<?= $this->section('header') ?>
<?= view('site/partials/utilities/header', [
    'eyebrow'     => 'Security utility',
    'description' => 'Fresh credentials, generated on demand.',
]) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto w-full max-w-6xl px-6 py-10" data-password-generator>
    <section class="utility-hero">
        <div class="max-w-2xl">
            <p class="font-mono text-sm font-bold uppercase tracking-[0.16em] text-acid">Password lab</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight sm:text-5xl">Passwords without guesswork.</h1>
            <p class="mt-4 max-w-xl text-lg text-base-content/65">
                A fresh batch for every length. Generated on the server with a cryptographically secure random source and never stored.
            </p>
        </div>

        <a href="<?= esc(site_url('password-generator')) ?>" class="btn btn-coral btn-lg shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
            </svg>
            Generate new batch
        </a>
    </section>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="password-panel" aria-labelledby="standard-passwords">
            <div class="password-panel__header">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 id="standard-passwords" class="text-2xl font-bold">Standard</h2>
                        <span class="badge badge-outline">Letters + numbers</span>
                    </div>
                    <p class="mt-2 text-sm text-base-content/60">Easy to type, with ambiguous characters removed.</p>
                </div>
            </div>

            <div class="password-list">
                <?php foreach ($passwords as $entry): ?>
                    <div class="password-entry" data-password-entry>
                        <label class="password-entry__length" for="standard-<?= esc((string) $entry['length'], 'attr') ?>">
                            <strong><?= esc((string) $entry['length']) ?></strong>
                            <span>chars</span>
                        </label>
                        <div class="password-entry__control">
                            <input
                                id="standard-<?= esc((string) $entry['length'], 'attr') ?>"
                                type="text"
                                class="password-entry__value"
                                value="<?= esc($entry['standard'], 'attr') ?>"
                                readonly
                                spellcheck="false"
                                autocomplete="off"
                                data-password-value>
                            <button
                                type="button"
                                class="password-copy"
                                aria-label="Copy <?= esc((string) $entry['length'], 'attr') ?> character standard password"
                                data-copy-password>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="8" y="8" width="12" height="12" rx="2" />
                                    <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </section>

        <section class="password-panel password-panel--strong" aria-labelledby="strong-passwords">
            <div class="password-panel__header">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 id="strong-passwords" class="text-2xl font-bold">Strong</h2>
                        <span class="badge badge-coral">+ symbols</span>
                    </div>
                    <p class="mt-2 text-sm text-base-content/60">Expanded character pool for higher entropy.</p>
                </div>
            </div>

            <div class="password-list">
                <?php foreach ($passwords as $entry): ?>
                    <div class="password-entry" data-password-entry>
                        <label class="password-entry__length" for="strong-<?= esc((string) $entry['length'], 'attr') ?>">
                            <strong><?= esc((string) $entry['length']) ?></strong>
                            <span>chars</span>
                        </label>
                        <div class="password-entry__control">
                            <input
                                id="strong-<?= esc((string) $entry['length'], 'attr') ?>"
                                type="text"
                                class="password-entry__value"
                                value="<?= esc($entry['strong'], 'attr') ?>"
                                readonly
                                spellcheck="false"
                                autocomplete="off"
                                data-password-value>
                            <button
                                type="button"
                                class="password-copy"
                                aria-label="Copy <?= esc((string) $entry['length'], 'attr') ?> character strong password"
                                data-copy-password>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="8" y="8" width="12" height="12" rx="2" />
                                    <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
    </div>

    <p class="sr-only" aria-live="polite" data-copy-status></p>
</main>
<?= $this->endSection() ?>
