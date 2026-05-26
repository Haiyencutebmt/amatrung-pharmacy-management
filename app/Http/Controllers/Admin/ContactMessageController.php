<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderByRaw("status = 'pending' DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.contact_messages.index', compact('messages'));
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved',
        ]);

        $contactMessage->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Cập nhật trạng thái yêu cầu hỗ trợ thành công.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Xóa yêu cầu hỗ trợ thành công.');
    }
}
