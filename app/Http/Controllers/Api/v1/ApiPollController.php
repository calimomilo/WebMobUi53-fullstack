<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiPollController extends Controller
{
    /**
     * Display a listing of the authenticated user's polls.
     */
    public function index(Request $request)
    {
        $polls = $request->user()->polls()->orderBy('created_at', 'desc')->with('options')->get();

        return $polls;
    }

    /**
     * Display the specified poll by its secret token.
     */
    public function show(string $token)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        return $poll;
    }
    
    /**
     * Create the specified poll.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:3|max:255',
            'title' => 'nullable|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.label' => 'required|string|min:1|max:255',
            'allow_multiple_choices' => 'boolean',
            'allow_vote_change' => 'boolean',
            'results_public' => 'boolean',
            'duration' => 'nullable|integer|min:60|max:8640000', // max: 100 jours
            'ends_at' => 'nullable|date|after:now',
            'is_draft' => 'boolean',
        ]);

        $publish = !$validated['is_draft'];

        $poll = $request->user()->polls()->create([
            'title' => $validated['title'],
            'question' => $validated['question'],
            'secret_token' => Str::uuid(),
            'is_draft' => $validated['is_draft'],
            'allow_multiple_choices' => $validated['allow_multiple_choices'],
            'allow_vote_change' => $validated['allow_vote_change'],
            'results_public' => $validated['results_public'],
            'duration' => $validated['duration'],
            'started_at' => $publish ? now() : null,
            'ends_at' => $validated['ends_at'] ?? ($publish && $validated['duration'] ? now()->addSeconds($validated['duration']) : null), // si une date est définie prend celle-là, sinon calcule en fonction de la durée si le sondage est publié
        ]);

        $poll->options()->createMany($validated['options']);

        return $poll;
    }

    /**
     * Update the specified poll.
     */
    public function update(Request $request, int $id)
    {
         $poll = Poll::find($id);

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        if ($poll->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$poll->is_draft) {
            return response()->json(['message' => 'Conflict : poll already published.'], 409);
        }

        $validated = $request->validate([
            'question' => 'required|string|min:3|max:255',
            'title' => 'nullable|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.label' => 'required|string|min:1|max:255',
            'allow_multiple_choices' => 'boolean',
            'allow_vote_change' => 'boolean',
            'results_public' => 'boolean',
            'duration' => 'nullable|integer|min:60|max:8640000', // max: 100 jours
            'ends_at' => 'nullable|date|after:now',
            'is_draft' => 'boolean',
        ]);

        $publish = !$validated['is_draft'];

        $poll->update([
            'title' => $validated['title'],
            'question' => $validated['question'],
            'is_draft' => $validated['is_draft'],
            'allow_multiple_choices' => $validated['allow_multiple_choices'],
            'allow_vote_change' => $validated['allow_vote_change'],
            'results_public' => $validated['results_public'],
            'duration' => $validated['duration'],
            'started_at' => $publish ? now() : null,
            'ends_at' => $validated['ends_at'] ?? $publish && $validated['duration'] ? now()->addSeconds($validated['duration']) : null, // si une date est définie prend celle-là, sinon calcule en fonction de la durée si le sondage est publié
        ]);

        $poll->options()->delete();        
        $poll->options()->createMany($validated['options']);

        return $poll;

    }

    /**
     * Remove the specified poll.
     */
    public function remove(Request $request, int $id)
    {
        $poll = Poll::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        $poll->delete();

        return response()->json(['message' => 'success'], 200);
    }
}
