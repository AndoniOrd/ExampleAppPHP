<!-- resources/views/filament/resources/email-template-preview.blade.php -->
@php
    $content = $showCode ? htmlspecialchars($emailTemplate->html_content) : $emailTemplate->html_content;
@endphp

<div class="p-4">
    @if($showCode)
        <pre class="p-4 bg-gray-100 rounded overflow-auto text-sm" style="max-height: 70vh;">{{ $content }}</pre>
    @else
        <div class="preview-container bg-white rounded shadow" style="max-height: 70vh; overflow: auto;">
            <!-- Email preview iframe to isolate CSS and prevent it from affecting the modal -->
            <iframe id="email-preview-frame" style="width: 100%; height: 600px; border: none;" srcdoc="{{ $emailTemplate->html_content }}"></iframe>
        </div>
        <script>
            // Adjust iframe height based on content
            document.getElementById('email-preview-frame').onload = function() {
                try {
                    const iframe = document.getElementById('email-preview-frame');
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    // Add base styles to ensure email renders properly
                    const style = iframeDoc.createElement('style');
                    style.textContent = `
                        body { margin: 0; padding: 10px; font-family: Arial, sans-serif; }
                        img { max-width: 100%; height: auto; }
                        table { max-width: 100%; }
                    `;
                    iframeDoc.head.appendChild(style);
                    
                    // Set iframe height based on content
                    setTimeout(() => {
                        const height = iframeDoc.body.scrollHeight;
                        iframe.style.height = Math.min(height + 20, 600) + 'px';
                    }, 100);
                } catch (e) {
                    console.error('Error adjusting iframe:', e);
                }
            };
        </script>
    @endif
</div>