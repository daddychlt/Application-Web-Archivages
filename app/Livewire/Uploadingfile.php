<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Document;
use App\Jobs\traitementQueueUploadFile;
use App\Models\User;
use App\Models\ActivityLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;


class Uploadingfile extends Component
{
    use WithFileUploads;

    public $file;
    public $mot_cle;
    public $service_id = [];
    public $users_confidence = [];
    public $confidence = false;
    public $progress = 0;

    

    protected $rules = [
        'mot_cle' => 'nullable|string|max:255', // Autorise null
        'service_id' => 'required|array|min:1',
        'users_confidence' => 'nullable|array',
        'file' => 'file|max:204800', // 20MB
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
    $this->validate([
        'file' => 'required|file|mimes:txt,pdf,doc,docx,xls,xlsx,csv,ppt,pptx,png,jpeg|max:1000200',
        'service_id' => 'required|array|min:1',
    ]);

    // Gestion du nom de fichier
    $originalName = $this->file->getClientOriginalName();
    $newName = $this->generateUniqueFilename($originalName);

    // Stockage du fichier
    $path = $this->file->store('archives', 'public');

    // Création du document
    $document = Document::create([
        'nom' => $newName,
        'filename' => $path,
        'type' => $this->file->getClientOriginalExtension(),
        'taille' => round($this->file->getSize() / 1024),
        'content' => '', // Contenu vide initialement
        "user_id" => Auth::id(),
        "confidentiel" => $this->confidence,
    ]);

    // Attachement des relations
    $document->services()->attach($this->service_id);

    if ($this->confidence) {
        $this->handleConfidentiality($document);
    }
    $fullPath = storage_path('app/public/' . $path);
    //$output = shell_exec("pdftotext -f 1 -l 5 $fullPath - 2>&1");
    //dd($output);
    // Dispatch du job
    traitementQueueUploadFile::dispatch($document, $this->mot_cle ?? '', $this->confidence);// Garantit une string vide si null
    

    // Journalisation
    ActivityLog::create([
        'action' => ' Début du traitement du document',
        'description' => $document->nom,
        'icon' => '...',
        'user_id' => Auth::id(),
        'confidentiel' => $this->confidence,
    ]);

    return redirect()->route('document')->with('success', 'Le document est en cours de traitement !');
}

private function generateUniqueFilename(string $originalName): string
{
    $counter = 1;
    $newName = $originalName;
    
    while (Document::where('nom', $newName)->exists()) {
        $newName = pathinfo($originalName, PATHINFO_FILENAME) 
                 . '_' . $counter++ 
                 . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
    }
    
    return $newName;
}

private function handleConfidentiality(Document $document)
{
    $document->confidentialite()->attach(Auth::user());
    
    if (!empty($this->users_confidence)) {
        $users = User::findMany($this->users_confidence);
        $document->confidentialite()->attach($users);
    }
}
   
    public function removeFile()
    {
        $this->reset('file', 'progress');
    }
    public function render()
    {
        return view('livewire.uploadingfile');
    }
}
