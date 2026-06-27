<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SupportTicket;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $q = $request->input('q');

        $results = [
            'students' => User::where('role', 'student')
                                ->where('name', 'LIKE', "%{$q}%")
                                ->take(5)->get(),
            'tickets'  => SupportTicket::where('subject', 'LIKE', "%{$q}%")
                                ->take(5)->get(),
        ];

        return view('admin.search-results', compact('results', 'q'));
    }
}