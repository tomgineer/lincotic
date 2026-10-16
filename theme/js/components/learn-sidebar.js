const openLearnSidebarEvent = 'lincotic:learn:open';
const closeLearnSidebarEvent = 'lincotic:learn:close';

function formatDate(value, fallback) {
    if (typeof value !== 'string' || !/^\d{4}-\d{2}-\d{2}/.test(value)) {
        return fallback;
    }

    const [year, month, day] = value.slice(0, 10).split('-').map(Number);
    const date = new Date(year, month - 1, day);

    return new Intl.DateTimeFormat('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(date);
}

function setTime(time, value, fallback) {
    if (!(time instanceof HTMLTimeElement)) {
        return;
    }

    time.textContent = formatDate(value, fallback);

    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
        time.dateTime = value;
    } else {
        time.removeAttribute('datetime');
    }
}

export function initLearnSidebar() {
    const sidebar = document.querySelector('[data-learn-sidebar]');

    if (!(sidebar instanceof HTMLElement) || typeof sidebar.showPopover !== 'function') {
        return;
    }

    const endpointTemplate = sidebar.dataset.endpointTemplate;
    const content = sidebar.querySelector('[data-learn-content]');
    const title = sidebar.querySelector('[data-learn-title]');
    const displayUrl = sidebar.querySelector('[data-learn-display-url]');
    const description = sidebar.querySelector('[data-learn-description]');
    const collection = sidebar.querySelector('[data-learn-collection]');
    const favorite = sidebar.querySelector('[data-learn-favorite]');
    const openLink = sidebar.querySelector('[data-learn-open-link]');
    const documentation = sidebar.querySelector('[data-learn-documentation]');
    const emptyDocumentation = sidebar.querySelector('[data-learn-documentation-empty]');
    const clickCount = sidebar.querySelector('[data-learn-click-count]');
    const lastOpened = sidebar.querySelector('[data-learn-last-opened]');
    const created = sidebar.querySelector('[data-learn-created]');
    const documentationUpdated = sidebar.querySelector('[data-learn-documentation-updated]');
    const closeButton = sidebar.querySelector('[popovertargetaction="hide"]');
    let requestController = null;
    let activeTrigger = null;

    if (!endpointTemplate
        || !(content instanceof HTMLElement)
        || !(title instanceof HTMLElement)
        || !(displayUrl instanceof HTMLElement)
        || !(description instanceof HTMLElement)
        || !(collection instanceof HTMLElement)
        || !(favorite instanceof HTMLElement)
        || !(openLink instanceof HTMLAnchorElement)
        || !(documentation instanceof HTMLElement)
        || !(emptyDocumentation instanceof HTMLElement)
        || !(clickCount instanceof HTMLElement)) {
        return;
    }

    const isOpen = () => sidebar.matches(':popover-open');

    const syncPageScroll = () => {
        document.documentElement.classList.toggle('overflow-hidden', isOpen());
    };

    const setTriggerBusy = (trigger, busy) => {
        if (!(trigger instanceof HTMLAnchorElement)) {
            return;
        }

        trigger.toggleAttribute('aria-busy', busy);
    };

    const render = (link) => {
        title.textContent = link.title ?? '';
        displayUrl.textContent = link.display_url ?? '';
        collection.textContent = link.collection_name ?? 'Unassigned';
        favorite.hidden = !link.is_favorite;

        const linkDescription = typeof link.description === 'string'
            ? link.description.trim()
            : '';
        description.textContent = linkDescription;
        description.hidden = linkDescription === '';

        openLink.href = link.follow_url ?? link.url ?? '#';

        const linkDocumentation = typeof link.documentation === 'string'
            ? link.documentation.trim()
            : '';
        documentation.hidden = linkDocumentation === '';
        emptyDocumentation.hidden = linkDocumentation !== '';
        documentation.innerHTML = linkDocumentation;

        clickCount.textContent = Number(link.click_count ?? 0).toLocaleString('en-GB');
        setTime(lastOpened, link.last_clicked_at, 'Never');
        setTime(created, link.created_at, 'Unknown');
        setTime(documentationUpdated, link.documentation_updated_at, '—');
        content.scrollTop = 0;
    };

    sidebar.addEventListener('toggle', syncPageScroll);

    document.addEventListener(closeLearnSidebarEvent, () => {
        requestController?.abort();

        if (isOpen()) {
            sidebar.hidePopover();
        }
    });

    document.addEventListener(openLearnSidebarEvent, async (event) => {
        const linkId = event.detail?.linkId;
        const trigger = event.detail?.trigger;

        if (!/^[1-9]\d*$/.test(linkId ?? '')) {
            return;
        }

        requestController?.abort();
        setTriggerBusy(activeTrigger, false);
        requestController = new AbortController();
        activeTrigger = trigger;
        setTriggerBusy(activeTrigger, true);

        if (isOpen()) {
            sidebar.hidePopover();
        }

        try {
            const response = await fetch(
                endpointTemplate.replace('__LINK_ID__', encodeURIComponent(linkId)),
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: requestController.signal,
                },
            );
            const payload = await response.json();

            if (!response.ok || !payload.link) {
                throw new Error(payload.error || 'Unable to load this link.');
            }

            render(payload.link);
            sidebar.showPopover();
            closeButton?.focus({ preventScroll: true });
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);
            }
        } finally {
            setTriggerBusy(trigger, false);

            if (activeTrigger === trigger) {
                activeTrigger = null;
            }
        }
    });

    syncPageScroll();
}
