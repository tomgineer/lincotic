<?= $this->extend('site/layouts/default') ?>

<?= $this->section('title') ?>Work Time Tracker | Lincotic<?= $this->endSection() ?>

<?= $this->section('header') ?>
<?= view('site/partials/navbar/navbar_utilities', [
    'eyebrow'     => 'Productivity utility',
    'description' => 'A private, local tracker for your working day.',
]) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto w-full max-w-6xl px-6 py-10" data-worktime-utility>
    <section class="utility-hero">
        <div class="max-w-2xl">
            <p class="font-mono text-sm font-bold uppercase tracking-[0.16em] text-acid">Workday lab</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight sm:text-5xl">Know exactly where your day stands.</h1>
            <p class="mt-4 max-w-xl text-lg text-base-content/65">
                Track eight net working hours with automatic break deductions. Your start time stays only in this browser.
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2 rounded-full border border-base-content/15 bg-base-100/60 px-4 py-2 text-sm text-base-content/60">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 text-acid" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 11l7 -7l7 7" /><path d="M5 13l7 7l7 -7" /></svg>
            Saved locally
        </div>
    </section>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(18rem,0.65fr)_minmax(0,1.35fr)]">
        <section class="utility-panel p-6" aria-labelledby="worktime-start-title">
            <h2 id="worktime-start-title" class="text-2xl font-bold">Start time</h2>
            <p class="mt-2 text-sm text-base-content/55">Set when you began. The tracker restores it after the page closes.</p>

            <label class="mt-6 block" for="worktime-start">
                <span class="mb-2 block text-sm font-semibold">When did you start?</span>
                <input
                    id="worktime-start"
                    type="time"
                    min="00:00"
                    max="23:59"
                    value="08:00"
                    class="input input-lg w-full bg-base-100 font-mono"
                    data-worktime-start>
            </label>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <button type="button" class="btn btn-primary" data-worktime-save>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5l9 -9" /></svg>
                    Start
                </button>
                <button type="button" class="btn btn-ghost" data-worktime-reset>Reset</button>
            </div>

            <p class="mt-5 text-sm text-base-content/55" aria-live="polite" data-worktime-state>No start time saved.</p>

            <div class="mt-6 border-t border-base-content/10 pt-5 text-sm text-base-content/60">
                <p><strong class="text-base-content">Break rules</strong></p>
                <p class="mt-2">30 minutes after 6 gross hours, plus another 15 minutes after 9 gross hours.</p>
            </div>
        </section>

        <section class="utility-panel overflow-hidden" aria-labelledby="worktime-clock-title">
            <div class="worktime-clock">
                <p id="worktime-clock-title" class="font-mono text-xs font-bold uppercase tracking-[0.2em] text-base-content/45">Net time worked</p>
                <output class="mt-4 block font-mono text-5xl font-bold tracking-tight text-acid sm:text-7xl" data-worktime-display>00:00:00</output>
                <p class="mt-4 min-h-6 text-center text-sm text-base-content/60" data-worktime-progress-text>Set a start time to begin tracking.</p>
            </div>

            <div class="px-6 pb-6 sm:px-8 sm:pb-8">
                <progress class="progress progress-primary h-3 w-full" value="0" max="100" data-worktime-progress></progress>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="worktime-stat">
                        <span>Progress</span>
                        <strong data-worktime-percent>0%</strong>
                    </div>
                    <div class="worktime-stat">
                        <span>Target end</span>
                        <strong data-worktime-end>--:--</strong>
                    </div>
                    <div class="worktime-stat">
                        <span>Break deducted</span>
                        <strong data-worktime-break>0 min</strong>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?= $this->endSection() ?>
