<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Dispute::with(['user', 'messages'])->latest();

        // If not staff, only show their own disputes
        if (!$user->isAdmin() && !$user->isManager() && !$user->isSupport()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('dispute_id', 'like', "%$search%")
                  ->orWhere('subject', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }

        $disputes = $query->paginate(15);
        
        return view('dashboard.disputes.index', compact('disputes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $dispute = Dispute::create([
            'dispute_id' => 'DS-' . strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
        ]);

        // Create initial message
        DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id' => auth()->id(),
            'message' => $request->description,
        ]);

        return back()->with('success', 'Dispute created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $dispute = Dispute::with(['user', 'messages.user'])->findOrFail($id);

        // Authorization check
        if (!$user->isAdmin() && !$user->isManager() && !$user->isSupport() && $dispute->user_id !== $user->id) {
            abort(403);
        }

        // Mark messages as read
        DisputeMessage::where('dispute_id', $dispute->id)
            ->where('user_id', '!=', $user->id)
            ->update(['is_read' => true]);

        // Update online status
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(2));

        return view('dashboard.disputes.chat', compact('dispute'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $dispute = Dispute::findOrFail($id);
        
        $message = DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'message_html' => view('dashboard.disputes.partials.message', ['message' => $message])->render()
            ]);
        }

        return back();
    }

    public function fetchMessages(Request $request, $id)
    {
        $last_message_id = $request->last_message_id;
        $user = auth()->user();
        $dispute = Dispute::findOrFail($id);

        // Update current user's online status
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(2));

        $newMessages = DisputeMessage::with('user')
            ->where('dispute_id', $id)
            ->where('id', '>', $last_message_id)
            ->get();

        $messages_html = '';
        foreach ($newMessages as $message) {
            $messages_html .= view('dashboard.disputes.partials.message', ['message' => $message])->render();
            
            // Mark as read if from other user
            if ($message->user_id !== $user->id) {
                $message->update(['is_read' => true]);
            }
        }

        // Determine "the other party"
        $isCustomer = ($user->id === $dispute->user_id);
        $otherUser = null;
        
        if ($isCustomer) {
            // If I am customer, the other is the staff who replied last
            $lastStaffMsg = $dispute->messages()
                ->where('user_id', '!=', $user->id)
                ->latest()
                ->first();
            if ($lastStaffMsg) {
                $otherUser = $lastStaffMsg->user;
            }
        } else {
            // If I am staff, the other is the customer
            $otherUser = $dispute->user;
        }

        $isTyping = false;
        $isOnline = false;
        if ($otherUser) {
            $isTyping = Cache::has('dispute-typing-' . $id . '-' . $otherUser->id);
            $isOnline = Cache::has('user-online-' . $otherUser->id);
        }

        return response()->json([
            'messages_html' => $messages_html,
            'last_message_id' => $newMessages->count() > 0 ? $newMessages->last()->id : $last_message_id,
            'is_typing' => $isTyping,
            'is_online' => $isOnline,
            'other_user_name' => $otherUser ? $otherUser->name : 'Support'
        ]);
    }

    public function updateTyping(Request $request, $id)
    {
        Cache::put('dispute-typing-' . $id . '-' . auth()->id(), true, now()->addSeconds(10));
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,resolved,closed',
        ]);

        $dispute = Dispute::findOrFail($id);
        $dispute->update(['status' => $request->status]);

        return back()->with('success', 'Dispute status updated.');
    }
}
