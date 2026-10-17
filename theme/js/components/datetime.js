import { copyInputValue, showCopyFeedback } from '../lib/clipboard.js';

const SECOND = 1000;
const MINUTE = 60 * SECOND;
const HOUR = 60 * MINUTE;
const DAY = 24 * HOUR;

const DEBT_BASELINES = {
    'debt-us': {
        date: new Date('2025-10-23T00:00:00Z'),
        amount: 38_000_000_000_000,
        perSecond: 63_000,
        currency: 'USD',
    },
    'debt-de': {
        date: new Date('2025-03-31T00:00:00Z'),
        amount: 2_520_000_000_000,
        perSecond: 1_800,
        currency: 'EUR',
    },
    'debt-gr': {
        date: new Date('2025-06-30T00:00:00Z'),
        amount: 403_000_000_000,
        perSecond: 160,
        currency: 'EUR',
    },
};

const integerFormatter = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

function isoWeek(date) {
    const utcDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const weekday = utcDate.getUTCDay() || 7;

    utcDate.setUTCDate(utcDate.getUTCDate() + 4 - weekday);

    const yearStart = new Date(Date.UTC(utcDate.getUTCFullYear(), 0, 1));

    return Math.ceil((((utcDate - yearStart) / DAY) + 1) / 7);
}

function dayOfYear(date) {
    const currentDay = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());
    const yearStart = Date.UTC(date.getFullYear(), 0, 1);

    return Math.floor((currentDay - yearStart) / DAY) + 1;
}

function elapsedParts(start, end) {
    const difference = Math.max(0, end - start);

    return {
        years: Math.floor(difference / (365.25 * DAY)),
        days: Math.floor(difference / DAY),
        hours: Math.floor(difference / HOUR),
        minutes: Math.floor(difference / MINUTE),
        seconds: Math.floor(difference / SECOND),
    };
}

function debtEstimate(baseline, now) {
    const elapsedSeconds = (now - baseline.date) / SECOND;
    const amount = baseline.amount + (baseline.perSecond * elapsedSeconds);

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: baseline.currency,
        maximumFractionDigits: 0,
    }).format(amount);
}

function metricValue(key, now) {
    const startOfYear = new Date(now.getFullYear(), 0, 1);
    const startOfNextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
    const startOfNextYear = new Date(now.getFullYear() + 1, 0, 1);
    const untilReset = elapsedParts(now, new Date('2030-01-01T00:00:00Z'));
    const sinceEvent = elapsedParts(new Date('2018-06-11T10:30:00Z'), now);
    const monthRemaining = elapsedParts(now, startOfNextMonth);
    const yearRemaining = elapsedParts(now, startOfNextYear);
    const yearElapsed = elapsedParts(startOfYear, now);

    const values = {
        'current-time': now.toLocaleTimeString('en-GB'),
        'current-date': now.toLocaleDateString('en-GB', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }),
        'current-week': `Week ${isoWeek(now)}`,
        'current-timestamp': String(Math.floor(now.getTime() / SECOND)),
        'day-of-year': integerFormatter.format(dayOfYear(now)),
        'minute-of-year': integerFormatter.format(yearElapsed.minutes),
        'second-of-year': integerFormatter.format(yearElapsed.seconds),
        'reset-days': integerFormatter.format(untilReset.days),
        'reset-hours': integerFormatter.format(untilReset.hours),
        'reset-seconds': integerFormatter.format(untilReset.seconds),
        'month-days': integerFormatter.format(monthRemaining.days),
        'month-hours': integerFormatter.format(monthRemaining.hours),
        'month-seconds': integerFormatter.format(monthRemaining.seconds),
        'year-days': integerFormatter.format(yearRemaining.days),
        'year-hours': integerFormatter.format(yearRemaining.hours),
        'year-seconds': integerFormatter.format(yearRemaining.seconds),
        'event-years': integerFormatter.format(sinceEvent.years),
        'event-days': integerFormatter.format(sinceEvent.days),
        'event-seconds': integerFormatter.format(sinceEvent.seconds),
        'unix-seconds': String(Math.floor(now.getTime() / SECOND)),
    };

    if (DEBT_BASELINES[key]) {
        return debtEstimate(DEBT_BASELINES[key], now);
    }

    return values[key] ?? 'N/A';
}

export function initDateTime() {
    const root = document.querySelector('[data-datetime-utility]');

    if (!(root instanceof HTMLElement)) {
        return;
    }

    const heroTime = root.querySelector('[data-datetime-hero-time]');
    const timezone = root.querySelector('[data-datetime-timezone]');
    const status = root.querySelector('[data-datetime-status]');
    const fields = root.querySelectorAll('[data-datetime-value]');

    if (timezone instanceof HTMLElement) {
        timezone.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local timezone';
    }

    const update = () => {
        const now = new Date();

        if (heroTime instanceof HTMLTimeElement) {
            heroTime.dateTime = now.toISOString();
            heroTime.textContent = now.toLocaleTimeString('en-GB');
        }

        fields.forEach((field) => {
            if (field instanceof HTMLInputElement) {
                field.value = metricValue(field.dataset.datetimeValue, now);
            }
        });
    };

    root.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-copy-datetime]');

        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        const input = button.closest('[data-copy-entry]')?.querySelector('[data-datetime-value]');

        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        try {
            await copyInputValue(input);
            showCopyFeedback(root, button, status, `${input.value} copied.`);
        } catch {
            if (status instanceof HTMLElement) {
                status.textContent = 'Unable to copy this value. Select it and copy manually.';
            }
        }
    });

    update();
    window.setInterval(update, SECOND);
}
