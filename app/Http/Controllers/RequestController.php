<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Database $database)
{
    $userId = Auth::id();

    // Obtener todas las peticiones
    $peticiones = $database->getReference('peticiones')->getValue();

    // Filtrar las peticiones por el ID del usuario autenticado
    $peticionesUsuario = [];

    if ($peticiones) {
        foreach ($peticiones as $key => $peticion) {
            if (isset($peticion['user_id']) && $peticion['user_id'] == $userId) {
                $peticion['id'] = $key; // opcional, por si necesitas el ID del nodo
                $peticionesUsuario[] = $peticion;
            }
        }
    }

    return view('request', [
        'peticiones' => $peticionesUsuario
    ]);
}

    public function showToday(Database $database)
{
    $hoy = now()->format('Y-m-d');

    $ref = $database->getReference('peticiones');
    $snapshot = $ref->getSnapshot();
    $peticiones = $snapshot->getValue();

    $peticionesHoy = [];

    if ($peticiones) {
        foreach ($peticiones as $id => $peticion) {
            if (isset($peticion['fecha']) && $peticion['fecha'] === $hoy) {
                $peticionesHoy[$id] = $peticion;
            }
        }
    }

    return view('request.index', ['peticiones' => $peticionesHoy]);
}


    

public function store(Request $request, Database $database)
{
    $data = $request->validate([
        'title' => 'required',
        'name' => 'required',
        'artist' => 'required',
    ]);

    $data['fecha'] = Carbon::now()->format('Y-m-d');
    $data['hora'] = Carbon::now()->format('H:i:s');
    $data['user_id'] = Auth::id(); // Aquí agregas el ID del usuario autenticado

    $newRequest = $database
        ->getReference('peticiones')
        ->push($data);

    return response()->json(['success' => true]);
}

public function loadMessages(Database $database)
{
    $startOfDay = now()->startOfDay()->timestamp;
    $endOfDay = now()->endOfDay()->timestamp;

    $snapshot = $database->getReference('mensajes')
        ->orderByChild('timestamp')
        ->startAt($startOfDay)
        ->endAt($endOfDay)
        ->getValue();

    $messages = collect($snapshot ?? [])->sortBy('timestamp')->values()->all();

    return response()->json($messages); // <--- Retornar JSON
}


public function sendMessage(Request $request, Database $database)
{
    \Log::info('Mensaje recibido:', $request->all());

    $message = trim($request->input('text'));
    $user = trim($request->input('user', 'Anonimo')); // Recoge el user enviado desde Flutter

    if ($message === '') {
        return response()->json(['error' => 'Mensaje vacío'], 400);
    }

    $data = [
        'user' => $user,
        'message' => $message,
        'timestamp' => now()->timestamp,
    ];

    $database->getReference('mensajes')->push($data);

    return response()->json(['success' => true]);
}




}
