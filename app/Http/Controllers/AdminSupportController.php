<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

class AdminSupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user','messages.user'])->findOrFail($id);
        return view('admin.support.show', compact('ticket'));
    }

    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = 'closed';
        $ticket->save();
        return redirect()->route('admin.support')->with('success', 'Ticket closed.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::with('user')->findOrFail($id);

        $msg = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
            'sender_role' => 'admin',
        ]);
        


        app(\App\Http\Controllers\NotificationController::class)->notifyUser(
         $ticket->user_id,
         'support_reply',
         'Support Reply from Admin',
         'Admin replied to your support ticket #' . $ticket->id,
         null
        );

        $ticket->status = 'answered';
        $ticket->save();

        if ($ticket->user && !empty($ticket->user->email)) {
            try {
                Mail::raw($request->input('message'), function ($m) use ($ticket) {
                    $m->to($ticket->user->email)->subject('Reply to your support ticket: ' . $ticket->subject);
                });
            } catch (\Exception $e) {
            }
        }

        return redirect()->route('admin.support.show', ['id' => $ticket->id])->with('success', 'Reply sent.');
    }
}
