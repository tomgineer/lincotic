<?php
$groups = [
    [
        'title' => 'Right now',
        'note'  => 'Live in your browser timezone.',
        'items' => [
            ['key' => 'current-time', 'label' => 'Current time'],
            ['key' => 'current-date', 'label' => 'Current date'],
            ['key' => 'current-week', 'label' => 'ISO week'],
            ['key' => 'current-timestamp', 'label' => 'Unix timestamp'],
        ],
    ],
    [
        'title' => 'Year progress',
        'note'  => 'Elapsed since January 1.',
        'items' => [
            ['key' => 'day-of-year', 'label' => 'Day of the year'],
            ['key' => 'minute-of-year', 'label' => 'Minute of the year'],
            ['key' => 'second-of-year', 'label' => 'Second of the year'],
        ],
    ],
    [
        'title' => 'Until 2030',
        'note'  => 'Countdown to January 1, 2030 UTC.',
        'items' => [
            ['key' => 'reset-days', 'label' => 'Days remaining'],
            ['key' => 'reset-hours', 'label' => 'Hours remaining'],
            ['key' => 'reset-seconds', 'label' => 'Seconds remaining'],
        ],
    ],
    [
        'title' => 'Period countdowns',
        'note'  => 'Time left in the current calendar periods.',
        'items' => [
            ['key' => 'month-days', 'label' => 'Days left this month'],
            ['key' => 'month-hours', 'label' => 'Hours left this month'],
            ['key' => 'month-seconds', 'label' => 'Seconds left this month'],
            ['key' => 'year-days', 'label' => 'Days left this year'],
            ['key' => 'year-hours', 'label' => 'Hours left this year'],
            ['key' => 'year-seconds', 'label' => 'Seconds left this year'],
        ],
    ],
    [
        'title' => 'Since the great event',
        'note'  => 'Elapsed since June 11, 2018 at 10:30 UTC.',
        'items' => [
            ['key' => 'event-years', 'label' => 'Full years'],
            ['key' => 'event-days', 'label' => 'Total days'],
            ['key' => 'event-seconds', 'label' => 'Total seconds'],
        ],
    ],
    [
        'title' => 'National Debt',
        'note'  => 'Estimated indicators targeting less than 1% deviation from official figures.',
        'items' => [
            ['key' => 'debt-us', 'label' => 'United States'],
            ['key' => 'debt-de', 'label' => 'Germany'],
            ['key' => 'debt-gr', 'label' => 'Greece'],
        ],
    ],
    [
        'title' => 'Unix reference',
        'note'  => 'The universal epoch counter.',
        'items' => [
            ['key' => 'unix-seconds', 'label' => 'Seconds since January 1, 1970'],
        ],
    ],
];
?>

<?= $this->extend('site/layouts/default') ?>

<?= $this->section('title') ?>Date & Time Calculator | Lincotic<?= $this->endSection() ?>

<?= $this->section('header') ?>
<?= view('site/partials/navbar/navbar_utilities', [
    'eyebrow'     => 'Time utility',
    'description' => 'Live clocks, milestones, and calendar counters.',
]) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="mx-auto w-full max-w-6xl px-6 py-10" data-datetime-utility>
    <section class="utility-hero">
        <div class="max-w-2xl">
            <p class="font-mono text-sm font-bold uppercase tracking-[0.16em] text-acid">DateTime lab</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight sm:text-5xl">See the clock from every angle.</h1>
            <p class="mt-4 max-w-xl text-lg text-base-content/65">
                Local time, calendar progress, long-range milestones, and the Unix epoch—updated every second.
            </p>
        </div>

        <div class="shrink-0 text-left lg:text-right">
            <time class="block font-mono text-4xl font-bold text-acid sm:text-5xl" data-datetime-hero-time>--:--:--</time>
            <span class="mt-2 block text-sm text-base-content/55" data-datetime-timezone>Local timezone</span>
        </div>
    </section>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
        <?php foreach ([array_slice($groups, 0, 3), array_slice($groups, 3)] as $column): ?>
            <div class="grid gap-6">
                <?php foreach ($column as $group): ?>
                    <section class="metric-card">
                        <header class="metric-card__header">
                            <h2 class="text-xl font-bold"><?= esc($group['title']) ?></h2>
                            <p class="mt-1 text-sm text-base-content/55"><?= esc($group['note']) ?></p>
                        </header>

                        <div class="metric-list">
                            <?php foreach ($group['items'] as $item): ?>
                                <div class="metric-row" data-copy-entry>
                                    <label for="datetime-<?= esc($item['key'], 'attr') ?>"><?= esc($item['label']) ?></label>
                                    <div class="utility-output">
                                        <input
                                            id="datetime-<?= esc($item['key'], 'attr') ?>"
                                            type="text"
                                            value="—"
                                            readonly
                                            spellcheck="false"
                                            data-datetime-value="<?= esc($item['key'], 'attr') ?>">
                                        <button
                                            type="button"
                                            class="utility-copy"
                                            aria-label="Copy <?= esc($item['label'], 'attr') ?>"
                                            data-copy-datetime>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </section>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>

    <p class="sr-only" aria-live="polite" data-datetime-status></p>
</main>
<?= $this->endSection() ?>
