<?php

namespace App\Livewire;

use Livewire\Component;

class Pdfview extends Component
{
    public $document;

    public function mount($document)
    {
        $this->document = $document;
    }

    public function render()
    {
        return view('livewire.pdfview');
    }
}
