<?php

namespace App\Livewire;

use Livewire\Component;

class Profile extends Component
{



    public $modifier=false;

    public function open()
    {
        $this->modifier = true;
    }

    public function close()
    {
        $this->modifier = false;
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
