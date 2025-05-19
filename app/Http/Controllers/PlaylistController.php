<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use getID3;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::latest()->get();
        return view('playlist.index', compact('playlists'));
    }

    public function create()
    {
        return view('playlist.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'songs.*.title' => 'required|string|max:255',
            'songs.*.artist' => 'nullable|string|max:255',
            'songs.*.scheduled_time' => 'nullable',
            'songs.*.file' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        try {
            $playlist = Playlist::create([
                'name' => $request->name,
            ]);

            foreach ($request->songs as $key => $song) {
                $file = $request->file("songs.$key.file");

                if ($file && $file->isValid()) {
                    $path = $file->store('audios', 'public');
                
                    $getID3 = new \getID3;
                    $fileInfo = $getID3->analyze($file->getRealPath());
                    $duration = isset($fileInfo['playtime_string']) ? $fileInfo['playtime_string'] : null;
                
                    $playlist->songs()->create([
                        'title' => $song['title'],
                        'artist' => $song['artist'] ?? null,
                        'scheduled_time' => $song['scheduled_time'] ?? null,
                        'file' => 'storage/' . $path,
                        'duration' => $duration,
                    ]);
                }
            }

            return redirect()->route('playlist.index')->with('success', 'Playlist created successfully');

        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', 'There was an error creating the playlist.');
        }
    }

    public function show(Playlist $playlist)
    {
        $songs = Song::where('playlist_id', $playlist->id)->get();
        return view('playlist.show', compact('playlist', 'songs'));
    }

    public function edit(Playlist $playlist)
    {
        $songs = $playlist->songs;
        return view('playlist.edit', compact('playlist', 'songs'));
    }

    public function update(Request $request, Playlist $playlist)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // You can validate song updates here if needed
        ]);

        $playlist->update(['name' => $request->name]);

        // Optional: Update songs if you want editable songs
        return redirect()->route('playlist.index')->with('success', 'Playlist updated successfully');
    }

    public function destroy(Playlist $playlist)
    {
        $playlist->delete();
        return response()->json(['message' => 'Playlist deleted successfully']);
    }

    public function applyPlaylist($id)
    {
        $playlist = Playlist::with('songs')->findOrFail($id);
        $filePath = "/home/andres/playlist.m3u";

        $paths = $playlist->songs->pluck('file')->toArray();

        file_put_contents($filePath, implode("\n", $paths));

        // Restart Liquidsoap to load the new playlist
        shell_exec("pkill liquidsoap && liquidsoap /home/andres/radio.liq &");

        return response()->json(['message' => 'Playlist applied successfully']);
    }
}
