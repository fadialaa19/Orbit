<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $applicationsQuery = $user->scholarshipApplications()
            ->with('scholarship')
            ->latest();

        // If you prefer pagination instead of get():
        // $applications = $applicationsQuery->paginate(10);
        $applications = $applicationsQuery->get();

        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', ScholarshipApplication::STATUS_PENDING)->count(),
            'processing' => $applications->where('status', ScholarshipApplication::STATUS_PROCESSING)->count(),
            'approved' => $applications->where('status', ScholarshipApplication::STATUS_APPROVED)->count(),
            'rejected' => $applications->where('status', ScholarshipApplication::STATUS_REJECTED)->count(),
        ];

        return view('dashboard.applications', [
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }
}

