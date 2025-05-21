@extends('layouts.master')

@section('content')
<div class="container mt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Playlists</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            🎙️ Ver Programación
        </a>
    </div>
    

    @if ($playlists->count())
        <div class="list-group">
            @foreach ($playlists as $playlist)
                <a href="{{ route('playlist.show', $playlist) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ $playlist->name }}</strong>
                        <small>{{ $playlist->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p>No hay playlists registradas.</p>
    @endif

    <a href="{{ route('playlist.create') }}" class="btn btn-primary mt-4">Crear nueva playlist</a>
</div>
@endsection
