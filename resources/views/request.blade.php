@extends('layouts.master')

@section('content')
<div class="container py-4">
    <h2>Petición de Canción</h2>

    <form action="{{ route('request.store')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tu nombre</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Título de la canción</label>
            <input type="text" class="form-control" name="title" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Artista</label>
            <input type="text" class="form-control" name="artist" required>
        </div>

        <button type="submit" class="btn btn-primary">Enviar Petición</button>
    </form>

    <hr>

    <h4 class="mt-5">Peticiones Registradas</h4>
    <ul id="requestsList" class="list-group mt-3">
        @forelse($peticiones as $peticion)
            <li class="list-group-item">
                <strong>{{ $peticion['title'] }}</strong> - {{ $peticion['artist'] }}
                <br>
                <small>Enviado a las {{ $peticion['hora'] }} del {{ $peticion['fecha'] }}</small>
            </li>
        @empty
            <li class="list-group-item">No has hecho ninguna petición hoy.</li>
        @endforelse
    </ul>
    
</div>
@endsection

