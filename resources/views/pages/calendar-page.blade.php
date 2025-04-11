<x-filament::page>
    {{-- Renderiza los widgets registrados automáticamente --}}
    @foreach ($this->getHeaderWidgets() as $widget)
        {{ $widget }} {{-- ✅ Usa $widget directamente --}}
    @endforeach

    {{-- Contenido adicional de la página --}}
    <div class="p-6">
        <!-- Tu contenido aquí -->
    </div>
</x-filament::page>