@props(['emailTemplate', 'showCode'])

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium">
                {{ $emailTemplate->subject_line ?? $emailTemplate->subject ?? 'No Subject' }}
            </h3>
            <p class="text-sm text-gray-500">
                Template: {{ $emailTemplate->name }}
            </p>
        </div>
        
        @if(request()->routeIs('filament.admin.resources.email-templates.preview'))
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
        @endif
    </div>

    @if (!$showCode)
        <div class="bg-white border border-gray-200 rounded shadow p-4 overflow-auto" style="min-height: 300px; color: #333;">
            {!! $emailTemplate->html_content ?? $emailTemplate->content ?? '<p>No content available</p>' !!}
        </div>
    @else
        <div class="bg-gray-100 rounded shadow p-4 overflow-auto max-h-[600px]">
            <pre class="text-sm" style="color: #333;">
                <code>{{ htmlspecialchars($emailTemplate->html_content ?? $emailTemplate->content ?? '') }}</code>
            </pre>
        </div>
    @endif
</div>