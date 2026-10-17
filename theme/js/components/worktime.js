const STORAGE_KEY = 'lincotic.workTimeStartAt';
const SECOND = 1000;
const MINUTE = 60 * SECOND;
const HOUR = 60 * MINUTE;
const TARGET_NET_HOURS = 8;

const pad = (value) => String(value).padStart(2, '0');

function formatDuration(milliseconds) {
    const totalSeconds = Math.floor(Math.max(0, milliseconds) / SECOND);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
}

function formatTime(date) {
    return `${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function breakDuration(elapsed) {
    let minutes = 0;

    if (elapsed >= 6 * HOUR) {
        minutes += 30;
    }

    if (elapsed >= 9 * HOUR) {
        minutes += 15;
    }

    return minutes * MINUTE;
}

function parseStartTime(value) {
    if (!/^\d{2}:\d{2}$/.test(value)) {
        return null;
    }

    const [hours, minutes] = value.split(':').map(Number);

    if (hours > 23 || minutes > 59) {
        return null;
    }

    const now = new Date();
    const start = new Date(now);
    start.setHours(hours, minutes, 0, 0);

    if (start - now > 12 * HOUR) {
        start.setDate(start.getDate() - 1);
    } else if (start > now) {
        return null;
    }

    return start;
}

function storedStartTime() {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        const date = value ? new Date(value) : null;

        return date && !Number.isNaN(date.getTime()) ? date : null;
    } catch {
        return null;
    }
}

export function initWorkTime() {
    const root = document.querySelector('[data-worktime-utility]');

    if (!(root instanceof HTMLElement)) {
        return;
    }

    const input = root.querySelector('[data-worktime-start]');
    const save = root.querySelector('[data-worktime-save]');
    const reset = root.querySelector('[data-worktime-reset]');
    const display = root.querySelector('[data-worktime-display]');
    const progress = root.querySelector('[data-worktime-progress]');
    const progressText = root.querySelector('[data-worktime-progress-text]');
    const percent = root.querySelector('[data-worktime-percent]');
    const end = root.querySelector('[data-worktime-end]');
    const breakOutput = root.querySelector('[data-worktime-break]');
    const state = root.querySelector('[data-worktime-state]');
    let startTime = storedStartTime();
    let persisted = startTime !== null;
    let timer;

    if (!(input instanceof HTMLInputElement) || !(save instanceof HTMLButtonElement)) {
        return;
    }

    const setText = (element, value) => {
        if (element instanceof HTMLElement) {
            element.textContent = value;
        }
    };

    const showIdle = () => {
        setText(display, '00:00:00');
        setText(progressText, 'Set a start time to begin tracking.');
        setText(percent, '0%');
        setText(end, '--:--');
        setText(breakOutput, '0 min');
        setText(state, 'No start time saved.');

        if (progress instanceof HTMLProgressElement) {
            progress.value = 0;
        }
    };

    const update = () => {
        if (!startTime) {
            showIdle();
            return;
        }

        const elapsed = Math.max(0, Date.now() - startTime.getTime());
        const deductedBreak = breakDuration(elapsed);
        const netWorked = Math.max(0, elapsed - deductedBreak);
        const target = TARGET_NET_HOURS * HOUR;
        const progressValue = Math.min(100, (netWorked / target) * 100);
        const roundedProgress = Math.round(progressValue);
        const targetEnd = new Date(startTime.getTime() + (8 * HOUR) + (30 * MINUTE));

        setText(display, formatDuration(netWorked));
        setText(percent, `${roundedProgress}%`);
        setText(end, formatTime(targetEnd));
        setText(breakOutput, `${deductedBreak / MINUTE} min`);
        setText(
            state,
            persisted
                ? `Started at ${formatTime(startTime)} · saved in this browser.`
                : `Started at ${formatTime(startTime)} · active in this tab only.`,
        );

        if (progress instanceof HTMLProgressElement) {
            progress.value = progressValue;
        }

        if (roundedProgress >= 100) {
            setText(progressText, 'Workday complete. Eight net hours reached.');
        } else if (roundedProgress >= 75) {
            setText(progressText, `Almost there. ${roundedProgress}% of your workday is complete.`);
        } else if (roundedProgress >= 50) {
            setText(progressText, `Good progress. ${roundedProgress}% of your workday is complete.`);
        } else {
            setText(progressText, `${roundedProgress}% of your eight-hour workday is complete.`);
        }
    };

    const run = () => {
        window.clearInterval(timer);
        update();
        timer = window.setInterval(update, SECOND);
    };

    save.addEventListener('click', () => {
        const parsed = parseStartTime(input.value);

        if (!parsed) {
            setText(state, 'Enter a valid start time.');
            return;
        }

        startTime = parsed;

        try {
            localStorage.setItem(STORAGE_KEY, startTime.toISOString());
            persisted = true;
        } catch {
            persisted = false;
        }

        run();
    });

    if (reset instanceof HTMLButtonElement) {
        reset.addEventListener('click', () => {
            startTime = null;
            persisted = false;
            window.clearInterval(timer);

            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch {
                // The in-memory tracker can still be reset.
            }

            showIdle();
        });
    }

    if (startTime) {
        input.value = formatTime(startTime);
        run();
    } else {
        showIdle();
    }
}
