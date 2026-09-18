<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChatUnreadController extends Controller
{
    public function count(): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'count' => 0,
                'conversation_id' => null,
            ]);
        }

        $unreadMessages = DB::table('ch_messages')
            ->join(
                'ch_conversation_participants',
                'ch_messages.conversation_id',
                '=',
                'ch_conversation_participants.conversation_id'
            )
            ->where(
                'ch_conversation_participants.user_id',
                $userId
            )
            ->where(
                'ch_messages.user_id',
                '!=',
                $userId
            )
            ->where(function ($query) {
                $query->whereNull(
                    'ch_conversation_participants.last_read_at'
                )->orWhereColumn(
                    'ch_messages.created_at',
                    '>',
                    'ch_conversation_participants.last_read_at'
                );
            })
            ->select([
                'ch_messages.id',
                'ch_messages.conversation_id',
                'ch_messages.created_at',
            ])
            ->orderByDesc('ch_messages.created_at')
            ->get();

        $count = $unreadMessages->count();

        $conversationId = $unreadMessages->first()?->conversation_id;

        return response()->json([
            'count' => $count,
            'conversation_id' => $conversationId,
        ]);
    }
}
