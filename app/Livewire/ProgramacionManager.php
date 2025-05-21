<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Song; // Tu modelo de canciones
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use App\Models\Playlist;
use App\Models\Programming;
use getID3;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Auth;

class ProgramacionManager extends Component
{
    public $programacion = []; // Lista de canciones programadas (IDs)
    public $radioEncendida = false;
    public $cancionActual = null; // Para destacar la canción que suena
    protected $pidFile = '/home/ubuntu/musica/liquidsoap.pid';
    public $startTimestamp = 0;    // cuándo arrancó la canción
    public $progressPercent = 0;   // porcentaje a mostrar
    use WithFileUploads;
    public $nuevoArchivo;
    public $busqueda = '';
    public $temaOscuro = false;
    public $nuevaCancion;
    public $nuevaDuracion;
    public $nuevaHora;
    public $archivoCancion;
    public $playlists;
    public $title, $artist, $playlist_id, $file, $position;
    public $messages = [];
    public $newMessage = '';
    protected $database;


    public function mount()
    {
        $this->database = app(Database::class);
        $this->programacion = Programming::with('song')
        ->orderBy('position')
        ->get()
        ->map(function ($item) {
            return [
                'song_id' => $item->song->id,
                'title' => $item->song->title,
                'artist' => $item->song->artist,
                'file' => $item->song->file,
                'duration' => $item->song->duration,
                'hora' => $item->hora, // si ya lo traías de la BD
            ];
        })->toArray();

        $this->playlists = Playlist::latest()->get();
        $this->checkRadioStatus();
        $this->loadMessages();
    }

    public function moveUp($index)
    {
        if ($index > 0) {
            $temp = $this->programacion[$index];
            $this->programacion[$index] = $this->programacion[$index - 1];
            $this->programacion[$index - 1] = $temp;
        }
    }

    public function moveDown($index)
    {
        if ($index < count($this->programacion) - 1) {
            $temp = $this->programacion[$index];
            $this->programacion[$index] = $this->programacion[$index + 1];
            $this->programacion[$index + 1] = $temp;
        }
    }

    public function removeSong($index)
    {
        if (isset($this->programacion[$index])) {
            $songData = $this->programacion[$index];

            // 1. Eliminar archivo de la carpeta
            $cleanTitle = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $songData['title']);
            $prefix = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $filePath = '/home/ubuntu/musica/' . "{$prefix} - {$cleanTitle}.mp3";

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // 2. Eliminar de la tabla 'programmings'
            if (isset($songData['song_id'])) {
                Programming::where('song_id', $songData['song_id'])->delete();
            }

            // 3. Eliminar del arreglo de programación (vista)
            unset($this->programacion[$index]);
            $this->programacion = array_values($this->programacion); // Reindexar

            // 4. Renombrar archivos que quedan en la carpeta para mantener orden
            $this->reordenarArchivosMusica();
        }
    }

    private function reordenarArchivosMusica()
    {
        $rutaMusica = '/home/ubuntu/musica/';

        foreach ($this->programacion as $index => $songData) {
            $cleanTitle = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $songData['title']);
            $prefix = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $newFileName = "{$prefix} - {$cleanTitle}.mp3";
            $newPath = $rutaMusica . $newFileName;

            // Buscar el archivo con cualquier número antiguo al inicio
            $pattern = $rutaMusica . '* - ' . $cleanTitle . '.mp3';
            foreach (glob($pattern) as $oldPath) {
                if ($oldPath !== $newPath) {
                    rename($oldPath, $newPath);
                }
            }
        }
    }


    public function saveProgramacion()
    {
        $rutaMusica = '/home/ubuntu/musica/';

        // 1. Eliminar archivos antiguos
        array_map('unlink', glob($rutaMusica . '*'));

        // 2. Eliminar programación existente
        Programming::truncate();

        // 3. Guardar nueva programación
        foreach ($this->programacion as $index => $songData) {
            if (isset($songData['song_id'])) {
                $song = Song::find($songData['song_id']);
                if ($song) {
                    // Ruta de origen (storage)
                    $source = storage_path('app/public/' . str_replace('storage/', '', $song->file));

                    // Nombre limpio para archivo
                    $cleanTitle = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $song->title);
                    $prefix = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    $destination = $rutaMusica . "{$prefix} - {$cleanTitle}.mp3";

                    
                    // Copiar archivo
                    if (file_exists($source)) {
                        copy($source, $destination);
                    }

                    // Guardar programación
                    Programming::create([
                        'song_id' => $song->id,
                        'scheduled_time' => $songData['hora'] ?? null,
                        'position' => $index + 1,
                    ]);

                }
            }
        }
        session()->flash('success', 'Programación guardada correctamente.');
    }



    public function encenderRadio()
    {
        $script = '/home/ubuntu/radio.liq';

        // Ejecutar Liquidsoap directamente y capturar el PID de forma segura
        $pid = shell_exec("/home/ubuntu/.opam/4.08.0/bin/liquidsoap {$script} > /dev/null 2>&1 & echo $!");

        // Guardar el PID en el archivo
        file_put_contents($this->pidFile, trim($pid));

        // Espera activa (máx. 5s) para confirmar que el proceso existe
        $attempts = 0;
        do {
            sleep(1);
            $this->checkRadioStatus();
            $attempts++;
        } while (!$this->radioEncendida && $attempts < 5);

        session()->flash(
            $this->radioEncendida ? 'success' : 'error',
            $this->radioEncendida
                ? 'Radio encendida correctamente.'
                : 'No se pudo iniciar la radio.'
        );
    }


    /**
     * Mata el proceso cuyo PID estaba registrado, elimina el PID file.
     */
    public function apagarRadio()
    {
        if (file_exists($this->pidFile)) {
            $pid = trim(file_get_contents($this->pidFile));

            // Matar proceso y su grupo
            shell_exec("kill -TERM -{$pid}");
            sleep(1); // Dar tiempo a que muera
            shell_exec("kill -KILL -{$pid}");

            // Borrar archivo PID
            @unlink($this->pidFile);
        }

        // Dar tiempo a que el proceso muera y actualizar estado
        sleep(1);
        $this->checkRadioStatus();

        session()->flash(
            !$this->radioEncendida ? 'success' : 'error',
            !$this->radioEncendida
                ? 'Radio detenida correctamente.'
                : 'No se pudo detener la radio.'
        );
    }


    /**
     * Comprueba si existe PID file y si ese PID sigue vivo.
     */
    public function checkRadioStatus()
    {
        if (file_exists($this->pidFile)) {
            $pid = trim(file_get_contents($this->pidFile));
            // kill -0 revisa si el proceso existe (retorna 0 si existe)
            $res = shell_exec("kill -0 {$pid} 2>&1; echo \$?");
            $this->radioEncendida = trim($res) === '0';
        } else {
            $this->radioEncendida = false;
        }

        $this->obtenerCancionActual();
    }

    private function limpiarTitulo($titulo)
    {
        // Extrae solo el nombre del archivo sin la extensión
        $nombreSinExtension = pathinfo(basename($titulo), PATHINFO_FILENAME);
    
        // Limpia caracteres especiales, convierte a minúsculas y elimina espacios sobrantes
        return mb_strtolower(trim(preg_replace('/[^a-zA-Z0-9-_ ]/', '', $nombreSinExtension)));
    }
    

    public function obtenerCancionActual()
    {
        $base        = '/home/ubuntu/musica/';
        $tfile       = $base . 'cancion_actual.txt';
        $sfile       = $base . 'cancion_start.txt';

        // 1) Título limpio
        $this->cancionActual = file_exists($tfile)
            ? $this->limpiarTitulo(file_get_contents($tfile))
            : null;

        // 2) Timestamp de arranque
        $this->startTimestamp = file_exists($sfile)
            ? (int) trim(file_get_contents($sfile))
            : 0;

        $this->progressPercent = 0;

        // 3) Si hay canción y timestamp válido, busca duración en tu programación
        if ($this->cancionActual && $this->startTimestamp > 0) {
            $song = collect($this->programacion)
                ->first(fn($s) => $this->limpiarTitulo($s['title']) === $this->cancionActual);

            if ($song && !empty($song['duration'])) {
                $durationSecs = $this->durationToSeconds($song['duration']);
                $elapsed      = time() - $this->startTimestamp;
                $this->progressPercent = $durationSecs > 0
                    ? min(100, intval($elapsed / $durationSecs * 100))
                    : 0;
            }
        }
    }


    private function durationToSeconds(string $str): int
    {
        $parts = explode(':', $str);
        if (count($parts) === 3) {
            return ((int)$parts[0]) * 3600 + ((int)$parts[1]) * 60 + ((int)$parts[2]);
        }
        if (count($parts) === 2) {
            return ((int)$parts[0]) * 60 + ((int)$parts[1]);
        }
        return (int)$parts[0];
    }

    public function agregarCancion($id)
    {
        $song = Song::find($id);

        if ($song) {
            // 1. Determinar la nueva posición en la programación
            $index = count($this->programacion) + 1; // +1 porque es base 1

            // 2. Agregar la canción al array programacion (vista)
            $this->programacion[] = [
                'song_id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'file' => $song->file,
                'duration' => $song->duration,
                'hora' => null,
            ];

            // 3. Crear registro en la base de datos
            Programming::create([
                'song_id' => $song->id,
                'scheduled_time' => null, // Si vas a asignar hora después
                'position' => $index,
            ]);

            // 4. Preparar nombre limpio para el archivo
            $cleanTitle = preg_replace('/[^a-zA-Z0-9-_ ]/', '', $song->title);
            $prefix = str_pad($index, 2, '0', STR_PAD_LEFT);
            $destination = '/home/ubuntu/musica/' . "{$prefix} - {$cleanTitle}.mp3";

            // 5. Copiar el archivo desde storage si existe
            $source = storage_path('app/public/' . str_replace('storage/', '', $song->file));

            if (file_exists($source)) {
                if (!file_exists($destination)) {
                    copy($source, $destination);
                }
            }
        }
    }



    public function resetearProgramacion()
    {
        $this->programacion = [];
        session()->flash('success', 'Programación vaciada correctamente.');
    }

    public function actualizarOrdenEnBD()
    {
        foreach ($this->programacion as $index => $songData) {
            if (isset($songData['id'])) {
                Song::where('id', $songData['id'])->update(['order' => $index]);
            }
        }

        session()->flash('success', 'Orden de programación guardado en base de datos.');
    }

    public function subirArchivoNuevaCancion()
    {
        $path = $this->nuevoArchivo->store('public/songs');
        $filename = basename($path);

        Song::create([
            'title' => pathinfo($filename, PATHINFO_FILENAME),
            'file' => 'storage/songs/' . $filename,
            'duration' => '00:00' // Estimar o editar manualmente
        ]);

        session()->flash('success', 'Canción subida y registrada.');
    }

    private function obtenerDuracionDesdeArchivo($path)
    {
        $output = shell_exec("ffprobe -i " . escapeshellarg($path) . " -show_entries format=duration -v quiet -of csv='p=0'");
        $segundos = intval(floatval(trim($output)));
        $min = str_pad(intval($segundos / 60), 2, '0', STR_PAD_LEFT);
        $sec = str_pad($segundos % 60, 2, '0', STR_PAD_LEFT);
        return "$min:$sec";
    }

    public function toggleTema()
    {
        $this->temaOscuro = !$this->temaOscuro;
    }

    public function getCancionesFiltradasProperty()
    {
        // Si no hay búsqueda, no retorna nada
        if (trim($this->busqueda) === '') {
            return collect(); // Retorna una colección vacía
        }

        // Si hay búsqueda, retorna coincidencias
        return Song::where('title', 'like', '%' . $this->busqueda . '%')
            ->orderBy('title')
            ->get();
    }

    public function registerAndAddSong()
    {
        // Validación de los campos
        $this->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'playlist_id' => 'required|exists:playlists,id', // Verifica que la playlist exista
            'file' => 'required|file|mimes:mp3,wav,ogg|max:10240', // Validación del archivo de música
            'position' => 'required|integer|min:1',
        ]);

        // Subir el archivo de la canción
        $path = $this->file->store('audios', 'public'); // Guardar en la carpeta 'audios' dentro de 'public'

        // Obtener la duración del archivo de audio
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($this->file->getRealPath());
        $duration = isset($fileInfo['playtime_string']) ? $fileInfo['playtime_string'] : null;
        $lastPosition = Programming::max('position') ?? 0;
        $newPosition = $lastPosition + 1;

        // Registrar la nueva canción en la base de datos
        $song = Song::create([
            'playlist_id' => $this->playlist_id,
            'title' => $this->title,
            'artist' => $this->artist,
            'file' => 'storage/' . $path, // Guardar la ruta del archivo
            'duration' => $duration, // Duración de la canción
        ]);

        // Agregar la canción a la programación con la posición
        Programming::create([
            'song_id' => $song->id,
            'position' => $newPosition,
        ]);

        // Limpiar los valores del formulario
        $this->title = null;
        $this->artist = null;
        $this->playlist_id = null;
        $this->file = null;
        $this->position = null;

        // Mensaje de éxito
        session()->flash('success', 'Canción registrada y agregada a la programación exitosamente.');
    }

    public function limpiarLista()
    {
        $this->programacion = []; // Vacía la programación en vivo
        session()->flash('success', 'Lista de programación limpiada.');
    }

    // Carga mensajes del día actual, ordenados por timestamp
    public function loadMessages()
    {
        $database = app(Database::class);
        $startOfDay = now()->startOfDay()->timestamp;
        $endOfDay = now()->endOfDay()->timestamp;

        $snapshot = $database->getReference('mensajes')
            ->orderByChild('timestamp')
            ->startAt($startOfDay)
            ->endAt($endOfDay)
            ->getValue();

        $this->messages = collect($snapshot ?? [])
            ->sortBy('timestamp')
            ->values()
            ->all();
    }

    public function sendMessage()
    {
        $database = app(Database::class);
        $msg = trim($this->newMessage);
        if ($msg === '') {
            return;
        }

        $user = Auth::user()->name ?? 'Anonimo';

        $data = [
            'user' => $user,
            'message' => $msg,
            'timestamp' => now()->timestamp,
        ];

        // Usar la instancia que viene como parámetro
        $database->getReference('mensajes')->push($data);

        $this->newMessage = '';

        // Refrescar mensajes
        $this->loadMessages();
    }

    protected $listeners = ['actualizarMensajes' => 'actualizarMensajes'];

    public function pollMensajes()
    {
        $this->loadMessages();
    }

    public function render()
    {
        $playlists = Playlist::all();
        $this->cancionesFiltradas = Song::latest()->take(40)->get();
        return view('livewire.programacion-manager', compact('playlists'));
    }
}
