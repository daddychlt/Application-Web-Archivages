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
                $documents = $alldocuments->filter(function ($document) use ($service) { return $document->services->contains($service); });
                $this->document_autor = $user->confidentialite()->pluck('nom')->toArray();
            }
        }

        return view('livewire.fast-search', ['documents' => $documents]);
    }
}
