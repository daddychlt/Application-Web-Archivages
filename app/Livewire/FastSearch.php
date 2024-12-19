<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FastSearch extends Component
{
    public $query = '';
    public $service;

    public $service_ident;

    public $document_autor;

    public function render()
    {

        $user = User::findOrFail(Auth::user()->id);
        $service = $user->service;
        $this->service_ident = $user->identificate;

        if (strlen($this->query) == 0) {
            $documents = [];
        } else {
            if($user->role->nom === "SuperAdministrateur")
            {
                $documents = Document::search(query:$this->query)->get();
            } else{
                $alldocuments = Document::search($this->query)->get();
                $service = $user->service;
                $documents_service = $alldocuments->filter(function ($document) use ($service) { return $document->services->contains($service); });
                $documents = $documents_service->where('confidentiel', false);
                $this->document_autor = $documents_service->whereIn('nom', $user->confidentialite()->pluck('nom'));
            }
        }

        return view('livewire.fast-search', ['documents' => $documents, 'document_autor' => $this->document_autor]);
    }
}
