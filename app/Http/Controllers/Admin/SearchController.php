<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\Scholarship;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $results = [
            'students' => [],
            'tickets' => [],
            'scholarships' => [],
        ];

        if ($q !== '') {
            $results['students'] = User::where('role', 'student')
                ->where('name', 'LIKE', "%{$q}%")
                ->orWhere(function ($query) use ($q) {
                    $query->where('role', 'student')->where('email', 'LIKE', "%{$q}%");
                })
                ->take(10)->get();

            $results['tickets'] = SupportTicket::with('user')
                ->where('subject', 'LIKE', "%{$q}%")
                ->take(10)->get();

            $results['scholarships'] = Scholarship::where('title_ar', 'LIKE', "%{$q}%")
                ->orWhere('title_en', 'LIKE', "%{$q}%")
                ->take(10)->get();
        }

        return view('admin.search-results', compact('results', 'q'));
    }
}