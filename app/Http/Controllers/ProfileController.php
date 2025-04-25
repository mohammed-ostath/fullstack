<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function index(){
        $user = User::with('profile','roles')->find(1);
        if(!Gate::forUser($user)->allows('admin-access')){
            return response()->json([
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        return response()->json([
            'profile' => $user->profile,
        ]);
    }
}
