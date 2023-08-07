<?php declare(strict_types=1);

namespace App\Factory;

use OpenAI;
use OpenAI\Client;

class OpenAIClientFactory
{
    private ?Client $client = null;

    public function __construct(private readonly string $openAIApiKey)
    {
    }

    public function client(): Client
    {
        if(null === $this->client) {
            $this->client = OpenAI::client($this->openAIApiKey);
        }

        return $this->client;
    }
}
