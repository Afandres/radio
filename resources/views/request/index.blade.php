@extends('layouts.master')

@section('content')
<div class="container py-4">
    <h1>Peticiones de Hoy</h1>

    @if(count($peticiones) > 0)
        <ul>
            @foreach($peticiones as $id => $peticion)
                <li>
                    <strong>{{ $peticion['title'] }}</strong> por {{ $peticion['name'] }} - {{ $peticion['artist'] }} <br>
                    Hora: {{ $peticion['hora'] }}
                </li>
            @endforeach
        </ul>
    @else
        <p>No hay peticiones registradas hoy.</p>
    @endif
</div>
@endsection

