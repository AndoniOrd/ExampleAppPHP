@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
  <h1 class="text-2xl mb-4">Progreso de Envío</h1>
  <div class="w-full bg-gray-200 rounded mb-2">
    <div id="bar" class="bg-blue-600 text-white text-center py-1" style="width:0%">0%</div>
  </div>
  <p id="status" class="text-sm text-gray-600">Esperando…</p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const campaignId = {{ $campaignId }};
  const bar        = document.getElementById('bar');
  const status     = document.getElementById('status');

  window.Echo.channel(`campaign.${campaignId}`)
    .listen('EmailProgressUpdated', e => {
      bar.style.width   = e.progress + '%';
      bar.textContent   = e.progress + '%';
      status.textContent = `Enviado ${e.progress}%`;
    });
});
</script>
@endpush