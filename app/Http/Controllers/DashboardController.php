<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $service = $user->service;

        if( $user->role == "SuperAdministrateur" ){
            $activities = ActivityLog::latest()->take(3)->get(); // Les 3 dernières actions
        } else {
            $activities = ActivityLog::latest()->whereIn('description', $service->documents()->pluck('nom'))->take(3)->get(); // Les 3 dernières actions
        };
        $users = User::all();
        $services = Service::all();
        $roles = Role::all();
        $documents = Document::all();

        return view('dashboard', compact('activities', 'users', 'services', 'roles', 'documents'));
    }
}
