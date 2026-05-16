<x-default-layout>
    <x-slot:scripts>
        @vite(['resources/js/poll-vote.js'])
    </x-slot>

    <x-slot:title>
        Votes
    </x-slot>

{{-- problem with @json blade directive not even joking i spent 3h on it
found a workaround here: https://github.com/laravel/framework/issues/56331 --}}
    <div
        id="app"
        data-props='{!! json_encode([
            "poll" => $poll,
            "isAuthenticated" => $isAuthenticated,
            "isOwner" => $isOwner,
            "votedIds" => $votedIds,
            "loginUrl" => route("login"),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}'
    ></div>

</x-default-layout>