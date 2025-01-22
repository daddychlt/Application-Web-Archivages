<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\User;

class DocumentSearch extends Component
{
    public $query = '';
    public $service;
    public $document_autor;

    public $documents_conf;

    public function mount($service)
    {
        $this->service = $service ;
    }

    public function render()
    {

        $user = User::findOrFail(Auth::user()->id);
        $service = $this->service;


        if (strlen($this->query) == 0) {
            $documents = [];
            $this->documents_conf = [];
        } else {
            if($service == null){

                if($user->role->nom === "SuperAdministrateur" | $user->role->nom === "Administrateur")
                {
                    // Rechercher les documents qui n'ont pas de service associé
                    $alldocuments = Document::search($this->query)->get();
                    // Filtrer les documents qui n'ont pas de service associé
                    $documents = $alldocuments->filter(function ($document) {return $document->services()->count() === 0;});
                } else {
                    $alldocuments = Document::search($this->query)->get();
                    $documents = $alldocuments->where('confidentiel', false);
                    $this->document_autor = $alldocuments->whereIn('nom', $user->confidentialite()->pluck('nom'));
                }

            } else {

                if($user->role->nom === "SuperAdministrateur" | $user->role->nom === "Administrateur")
                {
                    $alldocuments = Document::search(query:$this->query)->get();
                    $documents = $alldocuments->filter(function ($document) use ($service) { return $document->services->contains($service); });
                } else{
                    $alldocuments = Document::search($this->query)->get();
                    $documents_service = $alldocuments->filter(function ($document) use ($service) { return $document->services->contains($service); });
                    $documents = $documents_service->where('confidentiel', false);
                    $this->document_autor = $documents_service->whereIn('nom', $user->confidentialite()->pluck('nom'));
                }
            }
        }

        return view('livewire.document-search', ['documents' => $documents, 'document_autor' => $this->document_autor]);
    }
}
