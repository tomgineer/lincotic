function collectionItems(list) {
    return [...list.querySelectorAll('[data-sort-collection]')];
}

function updatePositions(list) {
    collectionItems(list).forEach((item, index) => {
        const position = item.querySelector('[data-sort-collection-position]');

        if (position instanceof HTMLElement) {
            position.textContent = String(index + 1);
        }
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

        dialog.setAttribute('aria-busy', String(busy));
        saveButton.disabled = busy || items.length < 2;
        saveButton.textContent = busy ? 'Saving…' : 'Save order';

        items.forEach((item) => {
            item.draggable = !busy;
            item.toggleAttribute('aria-disabled', busy);
        });
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
            setStatus('');
            setBusy(false);

            if (!dialog.open) {
                dialog.showModal();
            }
        });
    });

    list.addEventListener('dragstart', (event) => {
        const item = event.target instanceof Element
            ? event.target.closest('[data-sort-collection]')
            : null;

        if (!(item instanceof HTMLLIElement)) {
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
        if (!['ArrowUp', 'ArrowDown'].includes(event.key)) {
            return;
        }

        const item = event.target instanceof Element
            ? event.target.closest('[data-sort-collection]')
            : null;

        if (!(item instanceof HTMLLIElement)) {
            return;
        }

        const sibling = event.key === 'ArrowUp'
            ? item.previousElementSibling
            : item.nextElementSibling;

        if (!(sibling instanceof HTMLLIElement)) {
            return;
        }

        event.preventDefault();

        if (event.key === 'ArrowUp') {
            list.insertBefore(item, sibling);
        } else {
            list.insertBefore(sibling, item);
        }

        updatePositions(list);
        item.focus();
    });

    saveButton.addEventListener('click', async () => {
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
        setStatus('');
        setBusy(false);
    });
}
