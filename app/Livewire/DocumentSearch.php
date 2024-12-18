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

    public function render()
    {

        $user = User::findOrFail(Auth::user()->id);
        $service = $user->service;

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
                $documents = $documents_service->where('confidentiel', false)->orwhereIn('nom', $user->confidentialite()->pluck('nom'));
            }
        }

        return view('livewire.document-search', ['documents' => $documents, 'document_autor' => $this->document_autor]);
    }
}
