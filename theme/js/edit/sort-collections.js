function collectionItems(list) {
    return [...list.querySelectorAll('[data-sort-collection]')];
}

function updatePositions(list) {
    collectionItems(list).forEach((item, index) => {
        const position = item.querySelector('[data-sort-collection-position]');

        if (position instanceof HTMLElement) {
            position.textContent = String(index + 1);
        }

        const destination = item.querySelector('[data-sort-collection-destination]');

        if (destination instanceof HTMLInputElement) {
            destination.value = String(index + 1);
        }

        item.setAttribute('aria-label', `${item.dataset.collectionName}, position ${index + 1}`);
    });
}

export function initSortCollections() {
    const dialog = document.querySelector('[data-sort-collections-modal]');

    if (!(dialog instanceof HTMLDialogElement)) {
        return;
    }

    const list = dialog.querySelector('[data-sort-collections-list]');
    const saveButton = dialog.querySelector('[data-sort-collections-save]');
    const status = dialog.querySelector('[data-sort-collections-status]');
    const search = dialog.querySelector('[data-sort-collections-search]');
    const clearSearch = dialog.querySelector('[data-sort-collections-clear]');
    const count = dialog.querySelector('[data-sort-collections-count]');
    const empty = dialog.querySelector('[data-sort-collections-empty]');
    const hint = dialog.querySelector('[data-sort-collections-hint]');
    const scrollArea = dialog.querySelector('[data-sort-collections-scroll]');
    const endpoint = dialog.dataset.endpoint;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;

    if (!(list instanceof HTMLOListElement)
        || !(saveButton instanceof HTMLButtonElement)
        || !endpoint
        || !csrfHeader
        || !csrfToken) {
        return;
    }

    const initialOrder = collectionItems(list).map((item) => item.dataset.collectionId);
    let draggedItem = null;
    let isSaving = false;

    const searchQuery = () => search instanceof HTMLInputElement
        ? search.value.trim().toLocaleLowerCase()
        : '';

    const filterItems = () => {
        const query = searchQuery();
        const items = collectionItems(list);
        let visible = 0;

        items.forEach((item) => {
            item.hidden = !(item.dataset.collectionName ?? '').toLocaleLowerCase().includes(query);
            item.draggable = !isSaving && !query;
            item.classList.toggle('cursor-grab', !query);
            if (!item.hidden) visible += 1;
        });

        if (count instanceof HTMLElement) {
            count.textContent = query
                ? `${visible} of ${items.length} collections`
                : `${items.length} ${items.length === 1 ? 'collection' : 'collections'}`;
        }
        if (empty instanceof HTMLElement) empty.hidden = visible !== 0;
        if (clearSearch instanceof HTMLButtonElement) {
            clearSearch.disabled = isSaving || !(search instanceof HTMLInputElement) || !search.value;
        }
        if (hint instanceof HTMLElement) {
            hint.textContent = query
                ? 'Positions refer to the full list. Enter a position and press Move; clear search to drag.'
                : 'Enter a position and press Move. Drag or use ↑ / ↓ on a row; Home / End moves it first / last.';
        }
    };

    const setStatus = (message, isError = false) => {
        if (!(status instanceof HTMLElement)) {
            return;
        }

        status.textContent = message;
        status.classList.toggle('text-error', isError);
        status.classList.toggle('text-base-content/60', !isError);
    };

    const setBusy = (busy) => {
        const items = collectionItems(list);
        isSaving = busy;

        dialog.setAttribute('aria-busy', String(busy));
        saveButton.disabled = busy || items.length < 2;
        saveButton.textContent = busy ? 'Saving…' : 'Save order';

        items.forEach((item) => {
            item.toggleAttribute('aria-disabled', busy);
            item.querySelectorAll('input, button').forEach((control) => {
                control.disabled = busy;
            });
        });
        if (search instanceof HTMLInputElement) search.disabled = busy;
        filterItems();
    };

    const restoreInitialOrder = () => {
        const itemsById = new Map(
            collectionItems(list).map((item) => [item.dataset.collectionId, item]),
        );

        initialOrder.forEach((id) => {
            const item = itemsById.get(id);

            if (item) {
                list.append(item);
            }
        });

        updatePositions(list);
    };

    const moveItem = (item, position) => {
        if (isSaving) return;

        const items = collectionItems(list);
        const oldPosition = items.indexOf(item) + 1;
        const target = items[position - 1];
        if (!target) return;

        if (position !== oldPosition) {
            list.insertBefore(item, position > oldPosition ? target.nextElementSibling : target);
        }
        updatePositions(list);
        setStatus(`${item.dataset.collectionName} moved to position ${position} of ${items.length}.`);
    };

    const moveToDestination = (item) => {
        const destination = item.querySelector('[data-sort-collection-destination]');
        if (!(destination instanceof HTMLInputElement) || !destination.reportValidity()) return;
        moveItem(item, destination.valueAsNumber);
    };

    search?.addEventListener('input', () => {
        filterItems();
        if (scrollArea instanceof HTMLElement) scrollArea.scrollTop = 0;
    });

    clearSearch?.addEventListener('click', () => {
        if (!(search instanceof HTMLInputElement)) return;
        search.value = '';
        filterItems();
        if (scrollArea instanceof HTMLElement) scrollArea.scrollTop = 0;
        search.focus();
    });

    list.addEventListener('click', (event) => {
        const button = event.target instanceof Element
            ? event.target.closest('[data-sort-collection-move]')
            : null;
        const item = button?.closest('[data-sort-collection]');
        if (item instanceof HTMLLIElement) moveToDestination(item);
    });

    const updateCsrf = (result) => {
        if (typeof result.csrf === 'string' && result.csrf !== '') {
            csrfToken = result.csrf;
            dialog.dataset.csrfToken = result.csrf;
        }
    };

    const readResponse = async (response) => {
        let result = {};

        try {
            result = await response.json();
        } catch {
            // A generic message below handles non-JSON server responses.
        }

        updateCsrf(result);
        return result;
    };

    document.querySelectorAll('[data-sort-collections]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            restoreInitialOrder();
            if (search instanceof HTMLInputElement) search.value = '';
            setStatus('');
            setBusy(false);
            if (scrollArea instanceof HTMLElement) scrollArea.scrollTop = 0;

            if (!dialog.open) {
                dialog.showModal();
            }
        });
    });

    list.addEventListener('dragstart', (event) => {
        const item = event.target instanceof Element
            ? event.target.closest('[data-sort-collection]')
            : null;

        if (!(item instanceof HTMLLIElement) || isSaving || searchQuery()
            || (event.target instanceof Element && event.target.closest('input, button'))) {
            event.preventDefault();
            return;
        }

        draggedItem = item;
        item.setAttribute('aria-grabbed', 'true');
        item.classList.add('opacity-40');

        if (event.dataTransfer) {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', item.dataset.collectionId ?? '');
        }
    });

    list.addEventListener('dragover', (event) => {
        if (!(draggedItem instanceof HTMLLIElement)) {
            return;
        }

        event.preventDefault();

        if (event.dataTransfer) {
            event.dataTransfer.dropEffect = 'move';
        }

        const target = event.target instanceof Element
            ? event.target.closest('[data-sort-collection]')
            : null;

        if (!(target instanceof HTMLLIElement) || target === draggedItem) {
            return;
        }

        const bounds = target.getBoundingClientRect();
        const insertAfter = event.clientY > bounds.top + bounds.height / 2;
        list.insertBefore(draggedItem, insertAfter ? target.nextElementSibling : target);
        updatePositions(list);
    });

    list.addEventListener('drop', (event) => {
        event.preventDefault();
        updatePositions(list);
    });

    list.addEventListener('dragend', () => {
        if (draggedItem instanceof HTMLLIElement) {
            draggedItem.removeAttribute('aria-grabbed');
            draggedItem.classList.remove('opacity-40');
        }

        draggedItem = null;
        updatePositions(list);
    });

    list.addEventListener('keydown', (event) => {
        const item = event.target instanceof Element
            ? event.target.closest('[data-sort-collection]')
            : null;

        if (!(item instanceof HTMLLIElement) || isSaving) {
            return;
        }

        if (event.target instanceof HTMLInputElement) {
            if (event.key === 'Enter') {
                event.preventDefault();
                moveToDestination(item);
            }
            return;
        }

        if (event.target !== item || searchQuery()
            || !['ArrowUp', 'ArrowDown', 'Home', 'End'].includes(event.key)) return;

        event.preventDefault();
        const items = collectionItems(list);
        const position = items.indexOf(item) + 1;
        const nextPosition = {
            ArrowUp: Math.max(1, position - 1),
            ArrowDown: Math.min(items.length, position + 1),
            Home: 1,
            End: items.length,
        }[event.key];
        moveItem(item, nextPosition);
        item.focus();
        item.scrollIntoView({ block: 'nearest' });
    });

    saveButton.addEventListener('click', async () => {
        if (isSaving) return;
        const collectionIds = collectionItems(list).map((item) => item.dataset.collectionId);

        setStatus('Saving collection order…');
        setBusy(true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken,
                },
                body: JSON.stringify({ collection_ids: collectionIds }),
            });
            const result = await readResponse(response);

            if (!response.ok) {
                throw new Error(result.error || 'Unable to save the collection order.');
            }

            dialog.hidden = true;
            dialog.close();
            window.location.reload();
        } catch (error) {
            setStatus(error.message || 'Unable to save the collection order.', true);
            setBusy(false);
        }
    });

    dialog.addEventListener('close', () => {
        restoreInitialOrder();
        if (search instanceof HTMLInputElement) search.value = '';
        setStatus('');
        setBusy(false);
    });

    updatePositions(list);
    filterItems();
}
