const editorSelector = '[data-documentation-editor]';

const toggleCommands = {
    bold: (editor) => editor.chain().focus().toggleBold().run(),
    italic: (editor) => editor.chain().focus().toggleItalic().run(),
    underline: (editor) => editor.chain().focus().toggleUnderline().run(),
    strike: (editor) => editor.chain().focus().toggleStrike().run(),
    'bullet-list': (editor) => editor.chain().focus().toggleBulletList().run(),
    'ordered-list': (editor) => editor.chain().focus().toggleOrderedList().run(),
    blockquote: (editor) => editor.chain().focus().toggleBlockquote().run(),
    'code-block': (editor) => editor.chain().focus().toggleCodeBlock().run(),
};

const activeTypes = {
    bold: 'bold',
    italic: 'italic',
    underline: 'underline',
    strike: 'strike',
    'bullet-list': 'bulletList',
    'ordered-list': 'orderedList',
    blockquote: 'blockquote',
    'code-block': 'codeBlock',
};

function blockStyle(editor) {
    for (let level = 1; level <= 6; level++) {
        if (editor.isActive('heading', { level })) {
            return `heading-${level}`;
        }
    }

    return 'paragraph';
}

function syncControls(root, editor) {
    const blockStyleControl = root.querySelector('[data-editor-block-style]');

    if (blockStyleControl instanceof HTMLSelectElement) {
        blockStyleControl.value = blockStyle(editor);
    }

    root.querySelectorAll('[data-editor-command]').forEach((control) => {
        if (!(control instanceof HTMLButtonElement)) {
            return;
        }

        const command = control.dataset.editorCommand;
        const activeType = activeTypes[command];
        const active = activeType ? editor.isActive(activeType) : false;

        control.classList.toggle('btn-active', active);

        if (activeType) {
            control.setAttribute('aria-pressed', String(active));
        } else {
            control.removeAttribute('aria-pressed');
        }

        if (command === 'undo') {
            control.disabled = !editor.can().chain().focus().undo().run();
        } else if (command === 'redo') {
            control.disabled = !editor.can().chain().focus().redo().run();
        }
    });
}

function runCommand(editor, command) {
    if (toggleCommands[command]) {
        toggleCommands[command](editor);
    } else if (command === 'horizontal-rule') {
        editor.chain().focus().setHorizontalRule().run();
    } else if (command === 'undo') {
        editor.chain().focus().undo().run();
    } else if (command === 'redo') {
        editor.chain().focus().redo().run();
    }
}

function initEditor(root, tiptap) {
    const surface = root.querySelector('[data-editor-content]');
    const valueControl = root.querySelector('[data-editor-value]');
    const blockStyleControl = root.querySelector('[data-editor-block-style]');
    const toolbar = root.querySelector('[data-editor-toolbar]');

    if (!(surface instanceof HTMLElement)
        || !(valueControl instanceof HTMLTextAreaElement)) {
        return;
    }

    const createEditor = () => new tiptap.Editor({
        element: surface,
        extensions: [
            tiptap.StarterKit.configure({
                code: false,
                heading: { levels: [1, 2, 3, 4, 5, 6] },
                link: false,
            }),
        ],
        content: valueControl.value,
        editorProps: {
            attributes: {
                'aria-label': 'Documentation content',
                class: 'prose h-full max-w-none overflow-y-auto p-5 focus:outline-none',
            },
        },
        onCreate: ({ editor: currentEditor }) => syncControls(root, currentEditor),
        onSelectionUpdate: ({ editor: currentEditor }) => syncControls(root, currentEditor),
        onTransaction: ({ editor: currentEditor }) => syncControls(root, currentEditor),
        onUpdate: ({ editor: currentEditor }) => {
            valueControl.value = currentEditor.isEmpty ? '' : currentEditor.getHTML();
        },
    });

    let editor = createEditor();

    surface.hidden = false;
    valueControl.hidden = true;

    if (toolbar instanceof HTMLElement) {
        toolbar.hidden = false;
    }

    root.querySelectorAll('[data-editor-command]').forEach((control) => {
        control.addEventListener('click', () => {
            if (!editor) {
                return;
            }

            runCommand(editor, control.dataset.editorCommand);
            syncControls(root, editor);
        });
    });

    if (blockStyleControl instanceof HTMLSelectElement) {
        blockStyleControl.addEventListener('change', () => {
            if (!editor) {
                return;
            }

            const headingMatch = /^heading-([1-6])$/.exec(blockStyleControl.value);

            if (headingMatch) {
                editor.chain()
                    .focus()
                    .setHeading({ level: Number(headingMatch[1]) })
                    .run();
            } else {
                editor.chain().focus().setParagraph().run();
            }

            syncControls(root, editor);
        });
    }

    const form = root.closest('form');

    form?.addEventListener('reset', () => {
        queueMicrotask(() => {
            editor?.destroy();
            surface.replaceChildren();
            editor = createEditor();
            syncControls(root, editor);
        });
    });
}

export function initDocumentationEditors() {
    const tiptap = window.LincoticTiptap;

    if (!tiptap?.Editor || !tiptap?.StarterKit) {
        return;
    }

    document.querySelectorAll(editorSelector).forEach((root) => {
        if (root instanceof HTMLElement) {
            initEditor(root, tiptap);
        }
    });
}
