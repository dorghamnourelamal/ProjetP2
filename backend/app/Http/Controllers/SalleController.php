<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function index()
    {
        return response()->json(Salle::with('events')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'adresse' => 'nullable|string|max:255',
        ]);

        $salle = Salle::create($data);

        $this->activityLogger->log($request->user(), 'create', "Création de la salle '{$salle->nom}'", $request, 'Salle', $salle->id);

        return response()->json($salle, 201);
    }

    public function show(Salle $salle)
    {
        return response()->json($salle->load('events'));
    }

    public function update(Request $request, Salle $salle)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'adresse' => 'nullable|string|max:255',
        ]);

        $salle->update($data);

        $this->activityLogger->log($request->user(), 'update', "Modification de la salle '{$salle->nom}'", $request, 'Salle', $salle->id);

        return response()->json($salle);
    }

    public function destroy(Request $request, Salle $salle)
    {
        $nom = $salle->nom;
        $salle->delete();

        $this->activityLogger->log($request->user(), 'delete', "Suppression de la salle '{$nom}'", $request, 'Salle', $salle->id);

        return response()->json([
            'message' => 'Salle supprimée avec succès'
        ]);
    }
}
