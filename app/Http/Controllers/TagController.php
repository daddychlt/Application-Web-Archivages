<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

class TagController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'document-id' => 'required|int|exists:documents,id',
            'user-input' => 'required|string',
            'user-message' => 'required|string',
        ]);

        $document = Document::find($validatedData['document-id']);
        $tagger = Auth::user()->id;
        $taggedUsers = explode(' ', $validatedData['user-input']);
        $message = $validatedData['user-message'];

        $taggedUsers = array_unique($taggedUsers);

        // Ignorer les deux premiers caractères de chaque élément
        $taggedUsers = array_map(function ($user) {
            return substr($user, 2); // Enlève les 2 premiers caractères
        }, $taggedUsers);

        foreach ($taggedUsers as $userId) {
            // Enregistrez chaque tag dans la base de données
            $tagged = User::where('email', $userId)->first();
            if($tagged){
                $document->users()->attach([$tagged->id => ['tagger' => $tagger, 'message' => $message, 'new' => true]]);
            }
        }

        return redirect()->route('document')->with('success', 'Message envoyé avec succes');
    }

    public function index($id)
    {
        $document = Document::findOrFail($id);
        $users = User::all();
        $users_tag = User::where('id', '!=', Auth::id())->whereIn('service_id', $document->services->pluck('id'))->get();
        return view('tag', compact('document', 'users_tag'));
    }
}
