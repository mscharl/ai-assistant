<?php

namespace App\Controller;

use App\DTO\DaySummaryWithGreeting;
use App\Enums\TimeOfDayEnum;
use App\OpenAIGreetingsService;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class DaySummaryController extends BaseController
{
    public function __construct(private readonly CacheInterface $cache, private readonly OpenAIGreetingsService $greetingsService)
    {
    }

    #[Route('/day/summary', name: 'app_day_summary', stateless: true)]
    public function index(Request $request): Response
    {
        $timeOfDay = TimeOfDayEnum::tryFrom($request->query->get('tod'));
        $day = $request->query->get('day');

        if (!$timeOfDay) {
            throw new PreconditionFailedHttpException(sprintf('Missing time of day. "%s" seems to be invalid.', $request->query->get('tod')));
        }
        if (!$day) {
            throw new PreconditionFailedHttpException('Missing day information');
        }

        $cacheKey = $this->getCurrentRouteCacheKey($request, $timeOfDay->value);

        if ($request->query->getBoolean('forceReload')) {
            $this->cache->delete($cacheKey);
        }

        /** @var DaySummaryWithGreeting $daySummary */
        $daySummary = $this->cache->get(
            $cacheKey,
            function (CacheItemInterface $item) use ($day, $timeOfDay): DaySummaryWithGreeting {
                $endOfDay = new DateTimeImmutable('today 23:59:59');
                $item->expiresAt($endOfDay);

                $greeting = $this->greetingsService->getGreetings($timeOfDay);
                $summary = $this->greetingsService->summary($day);

                return new DaySummaryWithGreeting(greeting: $greeting, summary: $summary);
            },
        );

        $data = [
            'greeting' => $daySummary->greeting,
            'summary' => $daySummary->summary,
            'requests' => [
                'greeting' => $daySummary->greeting->request,
                'summary' => $daySummary->summary->request,
            ]
        ];

        $hash = hash(
            'sha3-512',
            sprintf(
                '%s:%s:%s',
                $daySummary->greeting->short,
                $daySummary->summary->content,
                $timeOfDay->value,
            ),
        );

        return $this->json(['hash' => $hash, 'data' => $data]);
    }
}
