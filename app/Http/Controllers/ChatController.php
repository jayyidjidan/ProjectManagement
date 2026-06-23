<?php

namespace App\Http\Controllers;

use App\Ai\Agents\ChatAgent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = auth()->user();
        $agent = new ChatAgent();

        $conversationId = session('chat_conversation_id');

        if ($conversationId) {
            $response = $agent
                ->continue($conversationId, as: $user)
                ->prompt($request->message);
        } else {
            $response = $agent
                ->forUser($user)
                ->prompt($request->message);

            session([
                'chat_conversation_id' => $response->conversationId
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => (string) $response,
        ]);
    }

    public function reset()
    {
        session()->forget('chat_conversation_id');

        return response()->json([
            'success' => true
        ]);
    }
}