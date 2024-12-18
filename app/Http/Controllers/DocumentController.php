<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getDocuments($serviceId)
    {
        $users_tag = User::where('id', '!=', Auth::id()) ->whereDoesntHave('role', function ($query) { $query->where('nom', 'SuperAdministrateur'); }) ->get();

        // Trouver le service et charger les documents associés
        if($serviceId == 0){
            $perPage = request('per_page', 10);
            $documents = Document::doesntHave('services')->paginate($perPage);
            $users = User::all();

            return view('documentShow', compact('documents', 'users', 'users_tag'));

        } else {

            $service = Service::find($serviceId);
            $users = User::all();

            if (!$service) {
                return response()->json(['error' => 'Erreur Service introuvable'], 404);
            }

            $perPage = request('per_page', 10);

            $user = User::findOrFail(Auth::user()->id);

            if($user->role->nom == "SuperAdministrateur"){
                $documents = $service->documents()->paginate($perPage);
            } else {
                // Récupérer les documents associés
                $documents = $service->documents()->where('confidentiel', false)->orwhereIn('nom', $user->confidentialite()->pluck('nom'))->paginate($perPage);
            }

            return view('documentShow', compact('documents', 'service', 'users', 'users_tag'));

        }
    }

    public function index()
    {
        $service = Auth::user()->service;
        $serviceIdent = Auth::user()->identificate;
        $documents = Document::all();
        $documentGene = Document::doesntHave('services')->get();
        $services = Service::all();
        return view('document', compact('documents', 'documentGene', 'service', 'serviceIdent', 'services'));
    }
}
