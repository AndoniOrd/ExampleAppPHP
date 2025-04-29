<x-filament-panels::page>
    {{-- Widgets section --}}
    @if ($headerWidgets = $this->getHeaderWidgets())
        <x-filament-widgets::widgets
            :widgets="$headerWidgets"
            :columns="$this->getHeaderWidgetsColumns()"
        />
    @endif

    {{-- Add this to hide the default table --}}
    <div class="hidden">
        {{ $this->table }}
    </div>

    @if ($footerWidgets = $this->getFooterWidgets())
        <x-filament-widgets::widgets
            :widgets="$footerWidgets"
            :columns="$this->getFooterWidgetsColumns()"
        />
    @endif
</x-filament-panels::page>