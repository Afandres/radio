@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Crear Playlist</h1>
        <a href="{{ route('playlist.index') }}" class="btn btn-outline-primary">
            📂 Playlist
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('playlist.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre de la Playlist</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div id="songs-container">
            <div class="song-group border p-3 rounded mb-3 position-relative">
                <h5>Canción</h5>
                <div class="mb-2">
                    <input type="text" name="songs[0][title]" class="form-control" placeholder="Título" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="songs[0][artist]" class="form-control" placeholder="Artista" required>
                </div>
                <div class="mb-2">
                    <input type="time" name="songs[0][scheduled_time]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <input type="file" name="songs[0][file]" class="form-control" accept=".mp3,.wav" required>
                </div>
                <!-- Botón para eliminar -->
                <button type="button" class="btn btn-danger mt-2 remove-song">Eliminar</button>
            </div>
        </div>

        <button type="button" class="btn btn-secondary" id="add-song">Agregar otra canción</button>

        <button type="submit" class="btn btn-success">Guardar Playlist</button>
    </form>
</div>

<script>
    let songIndex = 1;

    document.getElementById('add-song').addEventListener('click', function () {
        const container = document.getElementById('songs-container');
        const html = `
            <div class="song-group border p-3 rounded mb-3 position-relative">
                <h5>Canción</h5>
                <div class="mb-2">
                    <input type="text" name="songs[${songIndex}][title]" class="form-control" placeholder="Título" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="songs[${songIndex}][artist]" class="form-control" placeholder="Artista" required>
                </div>
                <div class="mb-2">
                    <input type="time" name="songs[${songIndex}][scheduled_time]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <input type="file" name="songs[${songIndex}][file]" class="form-control" accept=".mp3,.wav" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-song">Eliminar</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        songIndex++;
    });

    // Delegación de eventos para manejar clicks en botones "Eliminar"
    document.getElementById('songs-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-song')) {
            e.target.closest('.song-group').remove();
        }
    });
</script>
@endsection