<?php

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
    $events = json_decode(file_get_contents($url, false, $context), true);

    // Get all of the event type
    $eventTypes = [];

    foreach ($events as $event) {
        $eventTypes[] = $event['type'];
    }

    $score = 0;

    foreach ($eventTypes as $eventType) {
        switch ($eventType) {
            case 'PushEvent':
                $score += 5;
                break;
            case 'CreateEvent':
                $score += 4;
                break;
            case 'IssuesEvent':
                $score += 3;
                break;
            case 'CommitCommentEvent':
                $score += 2;
                break;
            default:
                $score++;
                break;
        }
    }

    return $score;
}

echo githubScore('pnlinh').PHP_EOL;
