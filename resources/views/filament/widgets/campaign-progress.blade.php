{{-- resources/views/dashboard/progress.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard de Envío</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-16 p-6 bg-white rounded shadow">
  <h1 class="text-2xl font-bold mb-4">Progreso de Envío de Correos</h1>

  <div id="progress" class="w-full bg-gray-200 rounded overflow-hidden mb-2">
    <div id="bar"
         class="bg-blue-600 text-white text-center py-1"
         style="width: 0%">0%</div>
  </div>
  <p id="status" class="text-sm text-gray-600">Esperando inicio…</p>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const campaignId = {{ $campaignId }};
    const bar        = document.getElementById('bar');
    const status     = document.getElementById('status');

    async function updateProgress() {
      try {
        const res = await fetch(`/campaigns/${campaignId}/progress`);
        if (!res.ok) throw new Error('Error de red');
        const { total, dispatched } = await res.json();
        const pct = total ? Math.round(dispatched / total * 100) : 0;
        bar.style.width   = pct + '%';
        bar.textContent   = pct + '%';
        status.textContent = `Enviados ${dispatched} de ${total}`;
      } catch (err) {
        console.error(err);
        status.textContent = 'Error al obtener progreso';
      }
    }

    updateProgress();
    setInterval(updateProgress, 3000);
  });
</script>

</body>
</html>
