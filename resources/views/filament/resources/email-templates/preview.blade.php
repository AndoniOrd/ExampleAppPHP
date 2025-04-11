<x-filament::page>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium">
                    {{ $record->subject_line ?? $record->subject ?? 'No Subject' }}
                </h3>
                <p class="text-sm text-gray-500">
                    Template: {{ $record->name }}
                </p>
            </div>

            <div>
                <a 
                    href="{{ url()->current() }}{{ $showCode ? '' : '?code=true' }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                           rounded-md font-semibold text-xs text-white uppercase tracking-widest
                           hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                           transition ease-in-out duration-150"
                >
                    {{ $showCode ? 'Show Preview' : 'View Code' }}
                </a>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section>
        @if (!$showCode)
            <div class="bg-white border border-gray-200 rounded shadow p-4" style="min-height: 400px; color: #333;">
                <div class="email-preview-container" style="max-width: 800px; margin: 0 auto;">
                    {!! $record->html_content ?? $record->content ?? '<p>No content available</p>' !!}
                </div>
            </div>
        @else
            <div class="bg-gray-100 rounded shadow p-4 overflow-auto max-h-[600px]">
                <pre class="text-sm" style="color: #333; white-space: pre-wrap;">
                    <code>{{ htmlspecialchars($record->html_content ?? $record->content ?? '') }}</code>
                </pre>
            </div>
        @endif
    </x-filament::section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove any form elements that might show up in the preview
        const previewContainer = document.querySelector('.email-preview-container');
        if (previewContainer) {
            const forms = previewContainer.querySelectorAll('form');
            forms.forEach(form => {
                const formContent = document.createElement('div');
                formContent.innerHTML = '<div class="text-gray-500 italic">[Form content would appear here]</div>';
                form.parentNode.replaceChild(formContent, form);
            });

            // Make sure all links don't actually work in the preview
            const links = previewContainer.querySelectorAll('a');
            links.forEach(link => {
                link.setAttribute('onclick', 'event.preventDefault();');
            });

            // Ensure text is visible by setting minimum contrast
            const styleTag = document.createElement('style');
            styleTag.textContent = `
                .email-preview-container * {
                    color: #333 !important;
                }
                .email-preview-container a {
                    color: #2563eb !important;
                    text-decoration: underline;
                }
                .email-preview-container {
                    background-color: #ffffff !important;
                }
            `;
            document.head.appendChild(styleTag);
        }
    });
    </script>
</x-filament::page>