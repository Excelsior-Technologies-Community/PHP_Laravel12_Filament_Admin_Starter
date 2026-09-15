<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $activities = LoginActivity::query()
            ->with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->whereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('user_agent', 'like', "%{$search}%");
                });
            })
            ->oldest('login_at')
            ->paginate(10)
            ->withQueryString();

        return view('login-activities', compact('activities'));
    }
}
