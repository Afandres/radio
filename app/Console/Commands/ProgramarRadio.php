<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProgramarRadio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'radio:programar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Selecciona la canción programada y la pasa a la carpeta de reproducción de Icecast';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ahora = \Carbon\Carbon::now()->format('H:i');
    
        $playlist = \App\Models\Playlist::latest()->first();
    
        if (!$playlist) {
            $this->info('No hay playlists disponibles');
            return;
        }
    
        $canciones = json_decode($playlist->songs, true);
    
        $cancionActual = collect($canciones)->first(function ($cancion) use ($ahora) {
            return $cancion['hora'] === $ahora;
        });
    
        if (!$cancionActual) {
            $this->info("No hay canción para la hora $ahora");
            return;
        }
    
        $rutaOrigen = public_path($cancionActual['archivo']);
        $rutaDestino = '/home/andres/musica/cancion.mp3';
    
        if (!\File::exists($rutaOrigen)) {
            $this->error("No se encontró el archivo: $rutaOrigen");
            return;
        }
    
        \File::copy($rutaOrigen, $rutaDestino);
    
        $this->info("Se copió la canción actual: {$cancionActual['titulo']} a $rutaDestino");
    }
}
