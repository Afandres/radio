@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Peticiones de Hoy</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            🎙️ Ver Programación
        </a>
    </div>

    @if(count($peticiones) > 0)
        <div class="list-group">
            @foreach($peticiones as $id => $peticion)
                <div class="list-group-item list-group-item-action mb-2 border rounded shadow-sm">
                    <h5 class="mb-1">
                        <strong>{{ $peticion['title'] }}</strong> 
                        <small class="text-muted">por {{ $peticion['artist'] }}</small>
                    </h5>
                    <p class="mb-1">Solicitado por: <strong>{{ $peticion['name'] }}</strong></p>
                    <small class="text-muted">
                        🕒 Hora: {{ \Carbon\Carbon::createFromFormat('H:i:s', $peticion['hora'])->format('h:i A') }}
                    </small>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            No hay peticiones registradas hoy.
        </div>
    @endif
</div>
@endsection

