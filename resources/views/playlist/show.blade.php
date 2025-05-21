@extends('layouts.master')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ $playlist->name }}</h1>

    @if ($songs)
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Artista</th>
                    <th>Hora programada</th>
                    <th>Reproducir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($songs as $song)
                    <tr>
                        <td>{{ $song->title }}</td>
                        <td>{{ $song->artist ?? 'Desconocido' }}</td>
                        <td>{{ $song->scheduled_time ? \Carbon\Carbon::parse($song->scheduled_time)->format('h:i A') : 'No definida' }}</td>
                        <td>
                            @if ($song->file)
                                <audio controls>
                                    <source src="{{ asset($song->file) }}" type="audio/mpeg">
                                    Tu navegador no soporta el elemento de audio.
                                </audio>
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning">No hay canciones en esta playlist.</div>
    @endif

    <a href="{{ route('playlist.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
</div>
@endsection