<?php

require_once 'vendor/autoload.php';

function githubScore($username)
{
    $url = "https://api.github.com/users/{$username}/events";

    /**
     * @see https://stackoverflow.com/questions/37141315/file-get-contents-gets-403-from-api-github-com-every-time
     */
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: PHP',
            ],
        ],
    ];

    $context = stream_context_create($opts);
    $events = collect(json_decode(file_get_contents($url, false, $context), true));

    return $events->pluck('type')->map(function ($eventType) {
        return collect([
            'PushEvent' => 5,
            'CreateEvent' => 4,
            'IssuesEvent' => 3,
            'CommitCommentEvent' => 2,
        ])->get($eventType, 1);
    })->sum();
}

echo githubScore('pnlinh').PHP_EOL;
