<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Document;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;

class AddDocServ extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:txt,pdf,doc,docx,xls,xlsx,csv,ppt,pptx,png,jpeg|max:51200')]
    public $file;
    public $mot_cle;
    public $service_id = [];
    public $users_confidence = [];
    public $confidence = false;
    public $progress = 0;

    protected $rules = [
        'mot_cle' => 'string',
        'service_id' => 'required|array|min:1',
        'users_confidence' => 'nullable|array',
    ];

    protected $message = [
        'service_id.required' => 'Selectionnez au moins un service',
    ];

    public $service;

    public function mount($service)
    {
        $this->service = $service;
        $this->service_id[] = $this->service->id;
    }

    public function save()
    {
        $this->validate();

        // Récupérer le fichier téléchargé
        $file = $this->file;

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

        $path = $this->file->store('archives', 'public');

        // Le chemin complet du fichier
        $fullPath = storage_path('app/public/' . $path);

        if ($this->file->getClientOriginalExtension() == 'pdf' or $this->file->getClientOriginalExtension() == 'PDF') {
            // Parse PDF file and build necessary objects
            $parser = new Parser();
            ini_set('memory_limit', '-1'); // Désactive la limite de mémoire
            try{
               $pdf = $parser->parseFile($fullPath); 
            }catch(\Exception $e){
                
            }
            
            
            
            // Récupère toutes les pages
                $pages = $pdf->getPages();
                

                // Initialiser une variable pour stocker le texte extrait
                $text = '';
                $compteError=0;
                $iteration=0;
                foreach ($pages as $page) {
                    // Extraire le texte de chaque page
                    try{
                      $text .= $page->getText();  
                    }catch(\Exception $e){
                        $text .=''; 
                        $compteError+=1;
                    }
                    $iteration+=1;
                }
                //dd('nombre_page='.count($pages).' nombre iteration='.$iteration.' nbre erreur='.$compteError);

          

            
        } elseif ($this->file->getClientOriginalExtension() == 'txt') {
            // Lire le contenu du fichier
            try{
                $text = file_get_contents($fullPath);
            }catch(\Exception $e){
                $text='';
            }
        }  elseif ($this->file->getClientOriginalExtension() == 'doc' or $this->file->getClientOriginalExtension() == 'docx') {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);
            $text = '';
            // Parcourir les sections et récupérer le texte
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        if (method_exists($element, 'getElements')) {
                            try{
                                $text .= $element->getText() . "\n"; // Ajouter le texte ligne par ligne
                            }catch(\Exception $e){
                                $text .='';
                            }
                            
                        }
                    }
                }
            }
        }elseif ($this->file->getClientOriginalExtension() == 'pptx' || $this->file->getClientOriginalExtension() == 'ppt') {
            $text = '';

                $objReader = \PhpOffice\PhpPresentation\IOFactory::createReader('PowerPoint2007');
                $presentation = $objReader->load($fullPath);
        
                foreach ($presentation->getAllSlides() as $slide) {
                    foreach ($slide->getShapeCollection() as $shape) {
                        if ($shape instanceof \PhpOffice\PhpPresentation\Shape\RichText) {
                            foreach ($shape->getParagraphs() as $paragraph) {
                                foreach ($paragraph->getRichTextElements() as $element) {
                                    try {
                                        if ($element instanceof \PhpOffice\PhpPresentation\Shape\RichText\TextElement) {
                                            $text .= $element->getText() . "\n";
                                        }
                                    } catch (\Exception $e) {
                                        continue; // Ignore l'erreur et passe à l'élément suivant
                                    }
                                }
                            }
                        }
                    }
                }
           
        }
         elseif ($this->file->getClientOriginalExtension() == 'xls' | $this->file->getClientOriginalExtension() == 'xlsx' | $this->file->getClientOriginalExtension() == 'csv') {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
            $text = '';
            foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $text .= $cell->getValue() . "\t";
                }
                $text .= "\n";
            }
        } else {
            $text = '';
        }

        

        $mot_cle = $this->mot_cle;

        $texts = $text . "\n" . $mot_cle;

        $document = Document::create([
            'nom' => $newName,
            'filename' => $path,
            'type' => $this->file->getClientOriginalExtension(),
            'taille' => round($this->file->getSize() / 1024),
            'content' => $texts,
            "user_id" => Auth::user()->id,
            "confidentiel" => $this->confidence,
        ]);

        $document->services()->attach($this->service_id);

        if ($this->confidence) {
            $document->confidentialite()->attach(Auth::user());
            foreach ($this->users_confidence as $user_id) {
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
            'confidentiel' => $this->confidence,
        ]);

        return redirect()->route('show_docs', $this->service->id)->with('success', 'Le fichier a été téléchargé avec succès sous le nom de ' . $newName);
    }

    public function removeFile()
    {
        $this->reset('file', 'progress');
    }

    public function render()
    {
        $services = Service::all();

        return view('livewire.add-doc-serv', [
            'services' => $services,
        ]);
    }
}
