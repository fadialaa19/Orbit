<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);
        $pendingCount = ContactMessage::pending()->count();

        return view('admin.contact-messages', compact('messages', 'pendingCount'));
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $request->validate(['status' => 'required|in:pending,read,resolved']);

        $contactMessage->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الرسالة');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return back()->with('success', 'تم حذف الرسالة');
    }
}
