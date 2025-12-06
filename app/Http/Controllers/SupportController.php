<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

class SupportController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $tickets = SupportTicket::with('messages')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.support', compact('tickets'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:4000',
        ]);

        $ticketData = [];
        if (Schema::hasColumn('support_tickets', 'user_id')) {
            $ticketData['user_id'] = $user->id;
        }
        if (Schema::hasColumn('support_tickets', 'subject')) {
            $ticketData['subject'] = $data['subject'];
        }
        if (Schema::hasColumn('support_tickets', 'message')) {
            $ticketData['message'] = $data['message'];
        }
        if (Schema::hasColumn('support_tickets', 'question')) {
            $ticketData['question'] = $data['message'];
        }
        if (Schema::hasColumn('support_tickets', 'source')) {
            $ticketData['source'] = $user->role ?? 'user';
        }
        if (Schema::hasColumn('support_tickets', 'role')) {
            $ticketData['role'] = $user->role ?? 'user';
        }
        if (Schema::hasColumn('support_tickets', 'status')) {
            $ticketData['status'] = 'open';
        }

        if (Schema::hasColumn('support_tickets', 'role') && !array_key_exists('role', $ticketData)) {
            $ticketData['role'] = $user->role ?? 'user';
        }

        try {
            if (empty($ticketData)) {
                $id = DB::table('support_tickets')->insertGetId([
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $ticket = SupportTicket::find($id);
            } else {
                $ticket = SupportTicket::create($ticketData);
            }
        } catch (QueryException $e) {
            if (stripos($e->getMessage(), "doesn't have a default value") !== false) {
                if (Schema::hasColumn('support_tickets', 'role')) {
                    $ticketData['role'] = $user->role ?? 'user';
                }
                $ticketData['updated_at'] = now();
                $ticketData['created_at'] = now();
                $id = DB::table('support_tickets')->insertGetId($ticketData);
                $ticket = SupportTicket::find($id);
            } else {
                throw $e;
            }
        }

        try {
            $message = SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $data['message'],
                'sender_role' => $user->role ?? 'user',
            ]);
        } catch (\Exception $e) {
            logger()->warning('Failed to save initial support message: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id
            ]);
        }      

    
        app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
            'support_message',
            'New Support Message',
            $user->first_name . ' ' . $user->last_name . ' sent a support message in ticket #' . $ticket->id,
            null
        );

        try {
            $to = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
            if ($to) {
                Mail::raw("User: {$user->email}\n\nMessage:\n" . $data['message'], function ($m) use ($to, $data) {
                    $m->to($to)->subject('[Support] ' . $data['subject']);
                });
            }
        } catch (\Exception $e) {
            logger()->error('Support email failed: ' . $e->getMessage(), ['ticket_id' => $ticket->id]);
        }

        return redirect()->route('support')->with('success', 'Support request submitted.');
    }
}
