<?php declare(strict_types=1);

namespace App;

use App\DTO\DaySummary;
use App\DTO\Greeting;
use App\Enums\TimeOfDayEnum;
use App\Factory\OpenAIClientFactory;

class OpenAIGreetingsService
{
    public function __construct(private readonly OpenAIClientFactory $openAIClientFactory)
    {
    }

    public function getGreetings(TimeOfDayEnum $timeOfDay): Greeting
    {
        $request = [];
//        $request[] = [
//            'role' => 'user',
//            'content' => sprintf('Schreibe eine kurze, maximal zwanzig wörter, humorvolle und familientaugliche Begrüßung passend zur Tageszeit %s', $timeOfDay->translated())
//        ];

//        $responseLong = $this->openAIClientFactory->client()->chat()->create([
//            'model' => 'gpt-3.5-turbo',
//            'messages' => $request,
//        ]);

//        $request[] = $responseLong->choices[0]->message->toArray();
//        $request[] = ['role' => 'user', 'content' => 'Bitte verkürze die Begrüßung auf maximal fünf Wörter.'];
        $request[] = ['role' => 'user', 'content' => sprintf('Begrüße uns als Familie in maximal fünf Wörtern passend zur Tageszeit %s', $timeOfDay->translated())];

        $responseShort = $this->openAIClientFactory->client()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $request,
        ]);

        return new Greeting(
            short: trim($responseShort->choices[0]->message->content, "\" \t\n\r\0\x0B"),
            request: $request,
        );
    }

    public function summary(string $day): DaySummary
    {
        $request = [];
        $request[] = [
            'role' => 'system',
            'content' => 'Ergänze den Text mit kurzen humorvollen Anmerkungen. Das Wetter und die Temperatur müssen erhalten bleiben. Der Text soll nicht länger als drei Sätze sein und in der Gegenwart formuliert werden.'
        ];
        $request[] = [
            'role' => 'user',
            'content' => $day,
        ];

        $response = $this->openAIClientFactory->client()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $request,
        ]);

        return new DaySummary(
            content: $response->choices[0]->message->content,
            request: $request,
        );
    }


}
