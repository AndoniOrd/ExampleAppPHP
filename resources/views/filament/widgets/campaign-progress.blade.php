<div class="p-6 bg-white rounded-lg shadow">
  <h2 class="text-xl font-semibold mb-4">Progreso de Envío</h2>
  
  <div class="w-full bg-gray-200 rounded-full mb-2">
    <div
      id="bar"
      class="bg-blue-600 text-white text-center py-1 rounded-full transition-all"
      style="width: 0%"
    >
      0%
    </div>
  </div>
  
  <p id="status" class="text-sm text-gray-500">Starting campaign...</p>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const campaignId = @js($campaignId);
    const bar        = document.getElementById('bar');
    const status     = document.getElementById('status');

    // Listen for campaign start
    window.Echo.channel(`campaign.${campaignId}`)
      .listen('CampaignStarted', () => {
        status.textContent = 'Processing campaign...';
      })
      // Listen for progress updates
      .listen('EmailProgressUpdated', e => {
        bar.style.width   = `${e.progress}%`;
        bar.textContent   = `${e.progress}%`;
        status.textContent = `Sent ${e.progress}%`;
      });
  });
</script>
@endpush
