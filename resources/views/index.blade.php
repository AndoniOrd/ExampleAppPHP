@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Correos Recibidos</h2>
            <div>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filtros
                </button>
            </div>
        </div>
        
        <div class="collapse" id="filterCollapse">
            <div class="card-body bg-light">
                <form action="{{ route('received-emails.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="provider_id" class="form-label">Proveedor</label>
                        <select name="provider_id" id="provider_id" class="form-select">
                            <option value="">Todos los proveedores</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ request('provider_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="is_read" class="form-label">Estado</label>
                        <select name="is_read" id="is_read" class="form-select">
                            <option value="">Todos</option>
                            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>No leídos</option>
                            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Leídos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                            value="{{ request('search') }}" placeholder="Asunto, remitente...">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="date_start" class="form-label">Fecha inicio</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" 
                            value="{{ request('date_start') }}">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="date_end" class="form-label">Fecha fin</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" 
                            value="{{ request('date_end') }}">
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Aplicar filtros</button>
                        <a href="{{ route('received-emails.index') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form action="{{ route('received-emails.batch') }}" method="POST" id="emailsForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input select-all" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="30"></th>
                                <th>Remitente</th>
                                <th>Asunto</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emails as $email)
                                <tr class="{{ $email->is_read ? '' : 'fw-bold' }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input email-checkbox" type="checkbox" 
                                                name="email_ids[]" value="{{ $email->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        @if(!$email->is_read)
                                            <span class="badge bg-primary rounded-pill">Nuevo</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $email->sender_name ? $email->sender_name . ' <' . $email->sender_email . '>' : $email->sender_email }}
                                    </td>
                                    <td>
                                        <a href="{{ route('received-emails.show', $email) }}" class="text-decoration-none">
                                            {{ $email->subject }}
                                        </a>
                                        @if($email->has_attachments)
                                            <i class="fas fa-paperclip text-muted ms-1"></i>
                                        @endif
                                    </td>
                                    <td>{{ $email->provider->name }}</td>
                                    <td>{{ $email->received_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('received-emails.show', $email) }}" 
                                                class="btn btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('received-emails.toggle-read', $email) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary" title="{{ $email->is_read ? 'Marcar como no leído' : 'Marcar como leído' }}">
                                                    <i class="fas fa-{{ $email->is_read ? 'envelope' : 'envelope-open' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('received-emails.destroy', $email) }}" method="POST" 
                                                onsubmit="return confirm('¿Está seguro de eliminar este correo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No se encontraron correos</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($emails->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="batch-actions">
                            <select name="action" class="form-select form-select-sm d-inline-block w-auto me-2">
                                <option value="">Acciones en lote</option>
                                <option value="mark_read">Marcar como leídos</option>
                                <option value="mark_unread">Marcar como no leídos</option>
                                <option value="delete">Eliminar</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary" id="applyBatch" disabled>Aplicar</button>
                        </div>
                        
                        <div>
                            {{ $emails->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestionar selección de todos los correos
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.email-checkbox');
        const applyBatchBtn = document.getElementById('applyBatch');
        
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBatchButton();
        });
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBatchButton();
                
                // Actualizar "select all" si todos los checkboxes están seleccionados
                const allChecked = [...checkboxes].every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });
        
        // Habilitar/deshabilitar botón de aplicar acción en lote
        function updateBatchButton() {
            const anyChecked = [...checkboxes].some(cb => cb.checked);
            applyBatchBtn.disabled = !anyChecked;
        }
        
        // Validar el formulario antes de enviarlo
        document.getElementById('emailsForm').addEventListener('submit', function(e) {
            const action = this.querySelector('select[name="action"]').value;
            if (!action) {
                e.preventDefault();
                alert('Por favor seleccione una acción para aplicar');
            }
        });
    });
</script>
@endpush