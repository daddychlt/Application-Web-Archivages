<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Document;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;

class Uploadingfile extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:pdf|max:10240')]
    public $file;
    public $mot_cle;
    public $service_id = [];
    public $users_confidence = [];
    public $confidence = false;

    protected $rules = [
        'mot_cle' => 'string',
        'service_id' => 'required|array|min:1',
        'users_confidence' => 'nullable|array',
    ];

    protected $message = [
        'service_id.required' => 'Selectionnez au moins un service',
    ];

    public $services;

    public function mount()
    {
        $this->services;
    }

    public function save()
    {
        $this->validate();

        // Récupérer le fichier téléchargé
        $file = $this->file;

        $service_id = $this->service_id;

        // Obtenir le nom original du fichier
        $originalName = $file->getClientOriginalName();

        // Créer un nom de fichier unique
        $newName = $originalName;
        $counter = 1;


        // Vérifier si un fichier avec ce nom existe déjà
        while (Document::where('nom', $newName)->exists()) {
            // Si le fichier existe, ajouter un suffixe "_2", "_3", etc.
            $newName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $counter . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
            $counter++;
        }
        if ($service_id===[]){
            return back()->with('error', 'Selectionné au moins un service');
        } else {
            $path = $this->file->store('archives', 'public');

            // Le chemin complet du fichier
            $fullPath = storage_path('app/public/' . $path);

            // Parse PDF file and build necessary objects
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            $mot_cle = $this->mot_cle;

            $texts = $text . ' ' . $mot_cle;

            $document = Document::create([
                'nom' => $newName,
                'filename' => $path,
                'type' => $this->file->getClientOriginalExtension(),
                'taille' => round($this->file->getSize() / 1024),
                'content' => $texts,
                "user_id" => Auth::user()->id,
                "confidentiel" => $this->confidence,
            ]);

            $document->services()->attach($service_id);

            if($this->confidence){
                $document->confidentialite()->attach(Auth::user());
                foreach($this->users_confidence as $user_id){
                    $user = User::findOrFail($user_id);
                    $document->confidentialite()->attach($user);
                }
            }

            // Lors de l'ajout d'un document
            ActivityLog::create([
                'action' => '✅ Document ajouté',
                'description' => $document->nom,
                'icon' => '✅',
                'user_id' => Auth::user()->id,
            ]);

            return redirect()->route('document')->with('success', 'Le fichier a été téléchargé avec succès sous le nom de ' . $newName);
        }
    }

    public function render()
    {
        return view('livewire.uploadingfile');
    }
}
