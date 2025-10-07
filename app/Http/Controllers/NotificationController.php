<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Ambil notifikasi terbaru (misal 50 terakhir)
    public function index()
    {
        $notifications = Notification::orderBy('created_at','desc')->take(50)->get();
        return response()->json($notifications);
    }

    // Simpan notifikasi baru
    public function store(Request $request)
    {
        $notif = Notification::create([
            'message' => $request->message,
        ]);
        return response()->json($notif);
    }

    public function destroy($id)
{
    $notification = Notification::findOrFail($id);
    $notification->delete();

    return response()->json(['success' => true]);
}

}
