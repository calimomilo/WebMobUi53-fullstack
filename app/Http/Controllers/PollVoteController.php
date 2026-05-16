<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollVoteController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        $user = $request->user();

        $isAuthenticated = !!$user;
        $isOwner = $isAuthenticated && $poll->user_id === $user->id;
        $votedIds = [];

        if($isAuthenticated) {
            $votedIds = PollVote::where('poll_id', $poll->id)->where('user_id', $user->id)->pluck('poll_option_id')->toArray();
        }

        if(!$isOwner || !$poll->results_public) {
            $poll->options->each->makeHidden('votes_count');
        }

        return view('polls.vote', [
            'poll' => $poll,
            'isAuthenticated' => $isAuthenticated,
            'isOwner' => $isOwner,
            'votedIds' => $votedIds,
        ]);
    }
}
