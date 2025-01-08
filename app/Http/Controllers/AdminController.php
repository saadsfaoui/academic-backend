<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Group;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Retourne le nombre total d'utilisateurs.
     */
    public function getUserCount()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }
        $userCount = User::count();
        return response()->json(['users' => $userCount]);
    }

    /**
     * Retourne le nombre total de groupes.
     */
    public function getGroupCount()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }
        $groupCount = Group::count();
        return response()->json(['groups' => $groupCount]);
    }

    /**
     * Retourne le nombre total de requêtes en attente.
     */
    public function getPendingRequestCount()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Only admins are allowed.'], 403);
        }
        $pendingRequestCount = Request::where('status', 'pending')->count();
        return response()->json(['pending_requests' => $pendingRequestCount]);
    }
}
