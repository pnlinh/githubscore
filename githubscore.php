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

    // Get all of the event type
    $eventTypes = $events->pluck('type');

    $scores = $eventTypes->map(function ($eventType) {
        $eventScores = [
            'PushEvent' => 5,
            'CreateEvent' => 4,
            'IssuesEvent' => 3,
            'CommitCommentEvent' => 2,
        ];

        if (! isset($eventScores[$eventType])) {
            return 1;
        }

        return $eventScores[$eventType];
    });

    return $scores->sum();
}

echo githubScore('pnlinh').PHP_EOL;
