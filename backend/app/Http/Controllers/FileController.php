<?php

namespace App\Http\Controllers;

use App\Models\Mongo\FileMeta;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    /**
     * Liste les fichiers, avec filtre optionnel par entité liée.
     * GET /api/files?related_type=Event&related_id=5
     */
    public function index(Request $request)
    {
        $query = FileMeta::query();

        if ($request->filled('related_type')) {
            $query->where('related_type', $request->query('related_type'));
        }

        if ($request->filled('related_id')) {
            $query->where('related_id', (string) $request->query('related_id'));
        }

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Affiche le contenu d'un fichier stocké.
     * GET /api/files/{id}/content
     */
    public function content(string $id)
    {
        $meta = FileMeta::findOrFail($id);

        if (! Storage::disk('public')->exists($meta->path)) {
            return response()->json([
                'message' => 'Fichier introuvable sur le disque.'
            ], 404);
        }

        $fullPath = Storage::disk('public')->path($meta->path);

        return response()->file($fullPath, [
            'Content-Type' => $meta->mime_type,
        ]);
    }

    /**
     * Upload d'un fichier + enregistrement de ses métadonnées dans MongoDB.
     * POST /api/files
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'related_type' => 'nullable|string|max:100',
            'related_id' => 'nullable|integer',
        ]);

        $file = $request->file('file');

        $filename = Str::uuid()
            . '_'
            . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '.'
            . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads', $filename, 'public');

        $meta = FileMeta::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'related_type' => $request->input('related_type'),
            'related_id' => $request->input('related_id'),
            'uploaded_by' => $request->user()?->id,
        ]);

        $this->activityLogger->log(
            $request->user(),
            'upload',
            "Téléversement du fichier '{$meta->original_name}'",
            $request,
            $request->input('related_type'),
            $request->input('related_id')
        );

        return response()->json([
            'meta' => $meta,
            'url' => route('files.content', ['id' => $meta->_id]),
        ], 201);
    }

    /**
     * Supprime un fichier et ses métadonnées.
     * DELETE /api/files/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $meta = FileMeta::findOrFail($id);

        if (Storage::disk('public')->exists($meta->path)) {
            Storage::disk('public')->delete($meta->path);
        }

        $meta->delete();

        $this->activityLogger->log(
            $request->user(),
            'delete',
            "Suppression du fichier '{$meta->original_name}'",
            $request
        );

        return response()->json(['message' => 'Fichier supprimé avec succès']);
    }
}
