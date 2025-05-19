// resources/js/ckeditor-config.js

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('FilamentLoadingFinished', function () {
        const editors = document.querySelectorAll('.filament-forms-ckeditor-field');

        if (!editors.length) {
            console.log('No CKEditor instances found');
            return;
        }

        editors.forEach((editor) => {
            const editorId = editor.getAttribute('id');

            if (!editorId) return;

            const existingEditor = window.CKEDITOR.instances[editorId];
            if (existingEditor) {
                console.log('Destroying existing CKEditor instance for:', editorId);
                existingEditor.destroy();
            }

            window.CKEDITOR.replace(editorId, {
                filebrowserUploadUrl: '/ckeditor/upload',
                filebrowserUploadMethod: 'form',
                uploadUrl: '/ckeditor/upload',

                extraPlugins: 'image2,uploadimage',

                image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
                image2_captionedClass: 'image-captioned',

                toolbar: [
                    { name: 'document', items: ['Source'] },
                    { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
                    },
                    {
                        name: 'paragraph',
                        items: [
                            'NumberedList',
                            'BulletedList',
                            '-',
                            'Outdent',
                            'Indent',
                            '-',
                            'Blockquote',
                            'CreateDiv',
                            '-',
                            'JustifyLeft',
                            'JustifyCenter',
                            'JustifyRight',
                            'JustifyBlock',
                        ],
                    },
                    { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
                ],

                allowedContent: true,
                height: 400,

                on: {
                    fileUploadRequest: function (evt) {
                        const fileLoader = evt.data.fileLoader;
                        const xhr = fileLoader.xhr;
                        const formData = new FormData();

                        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

                        formData.append('_token', token);
                        formData.append('upload', fileLoader.file);

                        xhr.open('POST', fileLoader.uploadUrl, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        xhr.send(formData);

                        evt.stop();
                    },
                },
            });

            console.log('CKEditor initialized for:', editorId);
        });
    });
});
