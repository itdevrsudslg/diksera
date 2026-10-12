<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Mengirim Pesan
    public function store(Request $request)
    {
        $request->validate(['message' => 'required|string|max:5000', 'receiver_id' => 'required|integer|exists:users,id']);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    // Ambil riwayat chat via Ajax untuk Modal
    public function getChat($userId)
    {
        $myId = Auth::id();
        $messages = Message::where(function($q) use ($myId, $userId) {
            $q->where('sender_id', $myId)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($myId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        // Tandai sudah dibaca
        Message::where('sender_id', $userId)->where('receiver_id', $myId)->update(['is_read' => true]);

        return response()->json($messages);
    }
    
    public function checkUnread()
{
    $count = Message::where('receiver_id', auth()->id())
        ->where('is_read', false)
        ->whereHas('sender', function($q) {
            $q->where('role', 'admin');
        })
        ->count();

    return response()->json(['unread' => $count]);
}
}