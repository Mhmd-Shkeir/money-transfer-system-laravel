<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use App\Models\Transaction;
use App\Models\AgentProfile;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    
    public function unreadCount()
    {
        $user = Auth::user();
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unreadCount' => $unreadCount]);
    }

    
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);
        
        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

   
    public function markAllAsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

 
    public function delete($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);
        
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted.');
    }

  public function deleteAll()
{
    $user = Auth::user();

    Notification::where('user_id', $user->id)->delete();

    return redirect()->back()->with('success', 'All notifications deleted.');
}

    public function getPanel()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'is_read' => $notif->is_read,
                    'created_at' => $notif->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'total' => Notification::where('user_id', $user->id)->count(),
        ]);
    }

     public function adminIndex()
    {
        $admin = Auth::user();

        if (!$admin || $admin->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $recentUsers = User::orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentTransactions = Transaction::with(['sender', 'beneficiary', 'fromCurrency', 'toCurrency'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $agentApplications = AgentProfile::with('user')
            ->where('is_approved', 0)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.notifications', compact(
            'recentUsers',
            'recentTransactions',
            'agentApplications'
        ));
    }

public function notifyAdmins($type, $title, $message, $relatedId = null)
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id'               => $admin->id,
                'type'                  => $type,
                'title'                 => $title,
                'message'               => $message,
                'related_transaction_id'=> $relatedId,
                'is_read'               => false,
            ]);
        }
    }
public function notifyAgents($type, $title, $message, $relatedId = null, $countryId = null)
{
    $agents = AgentProfile::where('is_approved', 1)
        ->with('user')
        ->get();

    $notified = [];

    foreach ($agents as $agent) {
        if (!$agent->user) continue;

        try {
            Notification::create([
                'user_id'               => $agent->user->id,
                'type'                  => $type,
                'title'                 => $title,
                'message'               => $message,
                'related_transaction_id'=> $relatedId,
                'is_read'               => false,
            ]);

            $notified[] = $agent->user->id;

        } catch (\Throwable $e) {
            \Log::warning('notifyAgents failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    \Log::info('notifyAgents (ALL AGENTS version) executed', [
        'type' => $type,
        'related_transaction_id' => $relatedId,
        'count' => count($notified),
        'user_ids' => $notified
    ]);
}

public function notifyUser($userId, $type, $title, $message, $relatedId = null)
{
    Notification::create([
        'user_id' => $userId,
        'type' => $type,
        'title' => $title,
        'message' => $message,
        'related_transaction_id' => $relatedId,
        'is_read' => false,
    ]);
}


}
