<div class="{{ $temaOscuro ? 'bg-gray-900 text-white' : 'bg-white text-black' }} container my-4" wire:poll.5s="checkRadioStatus">
  @livewireStyles

  <h2 class="mb-4 text-center">🎧 Gestor de Programación Radial</h2>

  {{-- Estado de la Radio + Acceso a Playlists --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      @if($radioEncendida)
        <button wire:click="apagarRadio" class="btn btn-danger">🛑 Apagar Radio</button>
      @else
        <button wire:click="encenderRadio" class="btn btn-success">▶️ Encender Radio</button>
      @endif
    </div>

    <div>
      <a href="{{ route('request') }}" class="btn btn-outline-primary">
        Canciones Solicitadas
      </a>
      <a href="{{ route('playlist.index') }}" class="btn btn-outline-primary">
        📂 Ver Playlists
      </a>
    </div>
  </div>


  {{-- Canción actual --}}
  @if($cancionActual)
    <div class="alert alert-primary">
      <strong>🎵 {{ $cancionActual }}</strong> está sonando
    </div>
  @else
    <div class="alert alert-secondary">No hay canción reproduciéndose actualmente.</div>
  @endif

  {{-- Sistema de mensajes --}}
  @if(session()->has('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @elseif(session()->has('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- Layout dividido en dos columnas --}}
  <div class="row mt-4">
    {{-- Columna izquierda: Añadir y buscar --}}
    <div class="col-md-4">
      <div class="mt-4 p-3 bg-light rounded shadow-sm">
        <h5 class="mb-3 text-primary">🎵 Buscar canción</h5>
    
        <input 
            type="text" 
            wire:model="busqueda" 
            placeholder="Buscar canción..." 
            class="form-control mb-3 border-primary"
        >
    
        <ul class="list-group">
            @forelse ($this->cancionesFiltradas as $cancion)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $cancion->title }}</span>
                    <button 
                        wire:click="agregarCancion({{ $cancion->id }})" 
                        class="btn btn-sm btn-outline-primary"
                    >
                        Agregar
                    </button>
                </li>
            @empty
                <li class="list-group-item text-muted text-center">
                    No se encontraron canciones.
                </li>
            @endforelse
        </ul>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-header bg-light">
          🎼 Añadir Canción
        </div>
        <div class="card-body">
          <form wire:submit.prevent="registerAndAddSong">
            <!-- Selección de Playlist -->
            <div class="form-group">
                <label for="playlist_id">Lista de Reproducción</label>
                <select wire:model="playlist_id" class="form-control" id="playlist_id" required>
                    <option value="">Seleccionar lista de reproducción</option>
                    @foreach($playlists as $playlist)
                        <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Título de la canción -->
            <div class="form-group mt-3">
                <label for="title">Título de la Canción</label>
                <input type="text" wire:model="title" class="form-control" id="title" placeholder="Ingrese el título de la canción" required />
            </div>

            <!-- Artista de la canción -->
            <div class="form-group mt-3">
                <label for="artist">Artista</label>
                <input type="text" wire:model="artist" class="form-control" id="artist" placeholder="Ingrese el nombre del artista" />
            </div>

            <!-- Carga del archivo de la canción -->
            <div class="form-group mt-3">
                <label for="file">Archivo de la Canción</label>
                <input type="file" wire:model="file" class="form-control" id="file" required />
            </div>

            <button class="btn btn-primary w-100 mt-3">Agregar</button>
        </form>
        </div>
      </div>
    </div>

    {{-- Columna derecha: Lista de programación --}}
    <div class="col-md-8">
      <div class="d-flex justify-content-between mb-2">
        <span class="fw-bold">🎙️ Programación ({{ count($programacion) }} canciones)</span>
        <div>
          {{-- <button wire:click="exportarJson" class="btn btn-outline-secondary btn-sm">📤 Exportar</button> --}}
          <button wire:click="limpiarLista" class="btn btn-outline-danger btn-sm">🧹 Limpiar</button>
        </div>
      </div>

      <table class="table table-bordered table-hover table-sm">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Canción</th>
            <th>Duración</th>
            <th>Artista</th>
            <th>🎛️</th>
          </tr>
        </thead>
        <tbody>
          @foreach($programacion as $i => $song)
            @php
              $title = preg_replace('/[^a-zA-Z0-9-_ ]/', '', mb_strtolower(trim($song['title'] ?? '')));
              $isNow = $cancionActual && $cancionActual === $title;
            @endphp
            <tr class="{{ $isNow ? 'table-success' : '' }}">
              <td>{{ $i + 1 }}</td>
              <td>
                {{ $song['title'] }}
                @if($isNow)
                  <span class="badge bg-success">En vivo</span>
                @endif
              </td>
              <td>{{ $song['duration'] ?? 'N/A' }}</td>
              <td>{{ $song['artist'] ?? 'N/A' }}</td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button wire:click="playNow({{ $i }})" class="btn btn-outline-success" title="Reproducir ahora">▶️</button>
                  <button wire:click="moveUp({{ $i }})" class="btn btn-outline-warning" title="Subir">⬆️</button>
                  <button wire:click="moveDown({{ $i }})" class="btn btn-outline-warning" title="Bajar">⬇️</button>
                  <button wire:click="removeSong({{ $i }})" class="btn btn-outline-danger" title="Eliminar">❌</button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  @livewireScripts
</div>
