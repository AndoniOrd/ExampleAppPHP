/**
 * Custom CKEditor configuration for email templates
 */

window.addEventListener('DOMContentLoaded', () => {
    // Ensure CKEditor and plugins are available
    if (typeof window.ClassicEditor === 'undefined' || !window.ClassicEditor.builtinPlugins) {
        console.warn('CKEditor not loaded or plugins undefined');
        return;
    }

    // If mergeTag plugin is not available, define custom implementation
    if (!window.ClassicEditor.builtinPlugins.has('mergeTag')) {
        class MergeTagUI {
            constructor(editor) {
                this.editor = editor;
                this._defineSchema();
                this._defineConverters();

                // Add toolbar button for merge tags
                editor.ui.componentFactory.add('mergeTag', (locale) => {
                    const dropdownView = createDropdown(locale);

                    // Define merge tags
                    const mergeTags = {
                        'First Name': '{first_name}',
                        'Last Name': '{last_name}',
                        Email: '{email}',
                        'Unsubscribe Link': '{unsubscribe_url}',
                    };

                    // Add buttons to dropdown
                    Object.entries(mergeTags).forEach(([label, tag]) => {
                        const button = this._createButton(label, tag, dropdownView);
                        dropdownView.add(button);
                    });

                    return dropdownView;
                });
            }

            _defineSchema() {
                // Define schema for merge tags if needed
            }

            _defineConverters() {
                // Define converters for merge tags if needed
            }

            _createButton(label, tag, dropdown) {
                // You should define how buttons are created and behave
                const button = new window.ClassicEditor.ui.button.ButtonView();
                button.set({
                    label,
                    withText: true,
                });

                button.on('execute', () => {
                    const editor = this.editor;
                    editor.model.change((writer) => {
                        const insertPosition = editor.model.document.selection.getFirstPosition();
                        writer.insertText(tag, insertPosition);
                    });
                });

                return button;
            }
        }

        // Register the plugin if needed
        // Note: This assumes you have a way to register external plugins
        // ClassicEditor.builtinPlugins.add('mergeTagPlugin', MergeTagUI);
    }

    // Listen for CKEditor initialization
    document.addEventListener('ckeditor:ready', () => {
        console.log('CKEditor has been initialized successfully');
    });
});
