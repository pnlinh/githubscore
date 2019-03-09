<?php

require_once 'vendor/autoload.php';

class GitHubScore
{
    /** @var */
    private $username;

    /**
     * GitHubScore constructor.
     *
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    public static function forUser($username)
    {
        return (new static($username))->score();
    }

    private function score()
    {
        return $this->events()
            ->pluck('type')
            ->map(function ($eventType) {
            return static::lookupScore($eventType);
        })->sum();
    }

    private function events()
    {
        $url = "https://api.github.com/users/{$this->username}/events";

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

        return collect(json_decode(file_get_contents($url, false, $context), true));
    }

    private function lookupScore($eventType)
    {
        return collect([
            'PushEvent' => 5,
            'CreateEvent' => 4,
            'IssuesEvent' => 3,
            'CommitCommentEvent' => 2,
        ])->get($eventType, 1);
    }
}

if (! isset($argv[1])) {
    $username = 'pnlinh';
} else {
    $username = $argv[1];
}

echo GitHubScore::forUser($username).PHP_EOL;
